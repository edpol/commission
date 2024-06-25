<?php

	function redirect_to( $location = NULL ) {
		if ($location != NULL) {
			header("Location: {$location}");
			exit;
		}
	}

	function format_line (&$line) {
		$line  = substr($line,0,-1);
//		$line .= "<br />";
	}

	function build_string ($row) {
		$line = array ("header" => "", "data" => "");
		foreach($row as $key => $value) {
/*			echo "\$key {$key} - \$value {$value} <br />";
			echo "Value = {$value} " . gettype($value) . "<br />";
			"boolean", "integer", "double", "string", "array", "object", "resource", "NULL", "unknown type"
*/			$line['header'] .= "{$key},";
			$line['data'] .= "{$value},";
		}
		format_line($line['header']);
		format_line($line['data']);
		return $line;
	}

	function change_date_format($mmddyyyy) {
//		in: mm/dd/yyyy -> out: yyyy-mm-dd
		return substr($mmddyyyy,6,4) . "-" . substr($mmddyyyy,0,2) . "-" .substr($mmddyyyy,3,2);
	}

	/*
	 * output to a csv file
	 */
	function print_selected ($rows, $db, $csvfile) {
		$print_heading = true;
		foreach($rows as $row) {
			$line = build_string($row);
			if ($print_heading) {
				writeln($csvfile, $line['header']);
				$print_heading = false; 
			}
			writeln($csvfile, $line['data']);
		}
	}

// * presence
// use trim() so empty spaces don't count
// use === to avoid false positives
// empty() would consider "0" to be empty
	function has_presence($value) {
		return isset($value) && $value !== "";
	}

	function validate_presences($required_fields) {
		global $errors;
		foreach($required_fields as $field) {
			$value = trim($_POST[$field]);
			if (!has_presence($value)) {
				$errors[$field] = "{$field} can't be blank";
			}
		}
	}

/*
 *	Password Functions
 */

	function password_encrypt($password) {
		$hash_format = "$2y$10$";	// Tells PHP to use Blowfish with a "cost" of 10
		$salt_length = 22; 			// Blowfish salts should be 22-characters or more
		$salt = generate_salt($salt_length);
		$format_and_salt = $hash_format . $salt;
		$hash = crypt($password, $format_and_salt);
		return $hash;
	}
	
	function generate_salt($length) {
		// Not 100% unique, not 100% random, but good enough for a salt
		// MD5 returns 32 characters
		$unique_random_string = md5(uniqid(mt_rand(), true));
	  
		// Valid characters for a salt are [a-zA-Z0-9./]
		$base64_string = base64_encode($unique_random_string);
	  
		// But not '+' which is valid in base64 encoding
		$modified_base64_string = str_replace('+', '.', $base64_string);
	  
		// Truncate string to the correct length
		$salt = substr($modified_base64_string, 0, $length);
	  
		return $salt;
	}

	function password_check($password, $existing_hash) {
		// existing hash contains format and salt at start
		$hash = crypt($password, $existing_hash);
		if ($hash === $existing_hash) {
			return true;
		} else {
			return false;
		}
	}

	function confirm_logged_in() {
		if (!isset($_SESSION['user_id'])) {
			redirect_to("index.php");
		}
	}

	/*
	 *	Report
	 */
	function writeln ($myfile, $line) {
			global $line_count;
			fwrite($myfile, $line . PHP_EOL);
			$line_count++;
	}

	/*
	 * output report the word format file
	 */
	function print_report($db, $rows, $count) {
		global $line_count, $previous_SALESPERSON, $max_lines, $commission_total, $sales_total;

		$file = getcwd()."/temp/report.doc";
		//echo "dir : " . __DIR__ . "<br />";
		//echo "File : " . $file . "<br />";

		$sql = "select * ";
		$sql .= "from #commission_table order by sales_id, inv_date ";
		//$stmt = $db->query($sql);

		$count == 1 ? $access = "w" : $access = "a";
		$myfile = fopen($file, $access) or die("Unable to open file {$file}!");
		$line_count = 0;
		$previous_SALESPERSON = '';

		foreach($rows as $row) {
			//	Next Sales Person
			if (($row['Sales ID'] <> $previous_SALESPERSON) && ($line_count>0)){
				print_footer("A",$db,$myfile,true);
				$commission_total = 0;
				$sales_total = 0;
			}
			$previous_SALESPERSON = $row['Sales ID'];
			$sp = $row["Sales Person"];
			if ($line_count<=1) {print_header($myfile,$sp);}
			print_row($myfile, $row);
			if ($line_count>=$max_lines) {print_footer("B",$db,$myfile);}
		}
		if ($line_count<$max_lines) {print_footer("C",$db,$myfile,true);}
		if ($count == 2) {fclose($myfile);}	// if you add another company, this has to be changed
	}


	function print_totals($db, $rows, $myfile) {
		global $page_count, $company, $start_date, $end_date;
		if ($page_count>0) writeln($myfile,"\f"); //chr(12)
		writeln($myfile,"                        Commission Report for {$company}");
		writeln($myfile,"Run Date : " . date("m/d/Y"));
		writeln($myfile,"Date Range : {$start_date} to {$end_date}");
		//$stmt = $db->query($sql . "order by sales_id group by sales_id ");

		writeln($myfile, "");
		$line = "Sales ID  TOTAL SALES  COMMISSION  SALESPERSON";
		writeln($myfile, $line);
		$commission_total = 0;
		$sales_total = 0;
		foreach($rows as $row) {
			$line  = sprintf('  %3s '  , $row['SALES_ID']);
			$line .= sprintf('%13.2f ' , $row['TOTAL_SALES']);
			$line .= sprintf('%11.2f ' , $row['COMMISSION']);
			$line .= "   " . $row["SALESPERSON"];
			writeln($myfile, $line);
			$commission_total += $row['COMMISSION']; 
			$sales_total += $row['TOTAL_SALES'];
		}
		$line = str_repeat(" ",10) . sprintf('%9.2f ' , $sales_total) . ' ' . sprintf('%10.2f ' , $commission_total);
		writeln($myfile, $line);
		page_number($myfile);
	}

	function print_row($myfile,$row) {
		global $commission_total, $commission_page, $sales_total, $sales_page;
		$line  = sprintf('%7d '   , $row['Cust ID']);
		$line .= sprintf('%6d '   , $row['Order']);
		$line .= sprintf('%7.2f ' , $row['Merch']);
		//$line .= sprintf('%4s '   , $row['SALES_ID']); 
		$line .= sprintf(' %s '   , $row['Paid']);
		$line .= sprintf('%5.1f%%', $row['Rate']);
		$line .= sprintf('%7.2f ' , $row['Comm']);
		$line .= substr($row['Name'],0,35); 
		writeln($myfile, $line);
		$commission_page  = $commission_page  + $row['Comm'];
		$commission_total = $commission_total + $row['Comm'];
		$sales_total += $row['Merch'];
		$sales_page  += $row['Merch'];
	}

	function print_header($myfile, $name) {
		global $start_date, $end_date, $page_count, $previous_SALESPERSON, $company;

		if ($page_count>0) fwrite($myfile,"\f"); //chr(12)
		writeln($myfile,"                          Commission Report for {$company}");
		writeln($myfile,"Run Date : " . date("m/d/Y"));
		writeln($myfile,"Date Range : {$start_date} to {$end_date}");
		writeln($myfile,"Sales Person : " . $previous_SALESPERSON . ", " . $name);
		writeln($myfile,"");
		writeln($myfile,"CUSTNUM ORDER#   MERCH  INV_DATE   RATE   COMM NAME ");
	}

/*
CUSTNUM,ORDERNO,MERCH,SALES_ID,INV_DATE,NAME,RATE,COMMISSION,SALESPERSON
38747,89930,149.95,004,08/04/2014,SARAH LEHNHOFF,2.50,3.75,Geralyn Falk                  

sales_id,Total Sales,Commission,SALESPERSON
004,299.90,7.50,Geralyn Falk                  

 */

	function print_footer($flag,$db,$myfile,$print_total=false) {
		global $page_count, $line_count, $max_lines, $previous_SALESPERSON, $commission_page, $commission_total, $sales_total, $sales_page;

//print totals
		$line = sprintf("%22.2f ", $sales_page) . str_repeat(" ",16) . sprintf("%22.2f ", $commission_page);
		
		writeln($myfile, $line);
		
		$commission_page = 0.00;
		$sales_page = 0.00;
		
		if ($print_total) {
			$line = sprintf("Grand Total %10.2f ", $sales_total) . str_repeat(" ",16) . sprintf("%22.2f ", $commission_total);
			//$line.=$flag;
			writeln($myfile, $line);
		}
		page_number($myfile);
	}

	function page_number($myfile) {
		global $max_lines, $line_count, $page_count;
		// Add Blank lines
		for($i = $max_lines-$line_count; $i >= 1; $i--) {
			writeln($myfile,"");
		}
		$page_count++;
		$line  = str_repeat(" ",30) . "Page {$page_count}";
		//$line .= "/{$line_count}";
		writeln($myfile, $line);
		$line_count=0;
	}


?>
