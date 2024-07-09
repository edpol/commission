<?php
	require_once ("../includes/initialize.php"); 
	confirm_logged_in(); 
	include("head_tag.php");
?>
<body>

<?php
	$conn = "";
	$go_back = '<p><a href="commission.php"><img src="images/back_arrow.jpg" class="no-border" alt="Back Arrow"/>Back</a></p>' . "<br />";
	// check that dates were selected
	if (isset($_POST['submit_dates'])) {

		unset($_POST['submit_dates']);

		$msg = "Start Date Not Selected " . $go_back;
		if (!isset($_POST['start_date'])) {
			die($msg);
		} else {
			if (empty($_POST['start_date'])) {
				die($msg);
			}
		}

		$msg = "End Date Not Selected " . $go_back;
		if (!isset($_POST['end_date'])) {
			die($msg);
		} 
		if (empty($_POST['end_date'])) {
		    die($msg);
		}

		$start_date = change_date_format($_POST['start_date']);
		$end_date   = change_date_format($_POST['end_date']);

		$date1 = strtotime($start_date);
		$date2 = strtotime($end_date);

		// if start date is larger than end, error
		$date3 = $date2-$date1;
		if ($date3 < 0) {
			die("dates are in reverse order " . $go_back);
		}

		// if start date is greater than today, error
		$date4 = time() - $date1;
		if ($date4 < 0) {
			die("dates are in the future " . $go_back);
		}

		/*
		 *	Dates worked, now start
		 */
		$last_occurrence = strripos(__FILE__,"\\");
		$dir = substr(__FILE__,0,$last_occurrence);
 		$file = $dir."/temp/report.csv";
 		$file = $_SERVER['DOCUMENT_ROOT']."/temp/report.csv";
 		$file = getcwd()."/temp/report.csv";

		$csvfile = fopen($file, "w") or die("Unable to open file {$file}!");
		$line = $start_date . " to " . $end_date;
		fwrite($csvfile, $line . PHP_EOL);

		$count = 0;
		$page_count = 0;
		$line_count = 0;
		$max_lines = 52;
		$db_list = array ("MDR" =>"MailOrderManager", "CCC" => "MOM-Clientele");

		foreach($db_list as $company => $dbname) {

			$page_total = 0.00;
			$commission_total = 0.00;
			$previous_SALESPERSON = '';

			$database = new sqlsrv($dbname);  
			$db =& $database;

			/*
			 *	Delete temporary table
			 */
			// $sql = "if object_id('tempdb..#commission_table') is not null drop table #commission_table";
			//$sql = "DROP TABLE IF EXISTS 'tempdb..#commission_table'";
			//$stmt = $db->query_db($sql);

			/*
			 *	select the invoices from the given date range
			 */
			$sql1  = "select invoice_id, invoice.CUSTNUM, invoice.ORDERNO, invoice.MERCH as MERCH ";
			$sql1 .=        ", cust.SALES_ID, invoice.PAID_DATE ";
			$sql1 .=        ", ltrim(rtrim(cust.FIRSTNAME)) + ' ' + ltrim(rtrim(cust.LASTNAME)) as NAME ";
			$sql1 .=        ", case when momuser.COMMGROSS IS NULL then 0 ELSE momuser.COMMGROSS end as RATE ";
			$sql1 .=        ", invoice.MERCH*momuser.COMMGROSS/100 as COMMISSION ";	
			$sql1 .=        ", ltrim(rtrim(MOMUSER.NAME)) as SALESPERSON ";
			$sql1 .= "from INVOICE ";
			$sql1 .= "inner join CUST    on invoice.custnum=cust.custnum ";
			$sql1 .= "left  join momuser on cust.SALES_ID=momuser.CODE ";
			$sql1 .= "where invoice.PAID=1 ";
			$sql1 .=   "and invoice.PAID_DATE > '{$start_date}' and invoice.PAID_DATE < '{$end_date}' ";
			$sql1 .=   "and invoice.MERCH<>0 ";


			$sql  = "select temp.invoice_id, temp.CUSTNUM as 'Cust ID', temp.ORDERNO as 'Order', temp.Merch ";
			$sql .=         ", temp.SALES_ID as 'Sales ID' "; 
			$sql .=         ", replace(convert(varchar, invoice.PAID_DATE, 10), '-', '/') as 'Paid' ";
			$sql .=         ", Name ";
			$sql .=         ", RATE as 'Rate' ";
			$sql .=         ", COMMISSION as 'Comm' ";
			$sql .=         ", case when SALESPERSON is null then '' else salesperson end as 'Sales Person' ";
			$sql .= "from INVOICE ";
			$sql .= "JOIN ( ";
			$sql .= $sql1;
			$sql .= ") as temp ";
			$sql .= "on temp.invoice_id=invoice.invoice_id ";
			$sql .= "where salesperson <> '' ";

			$rows = $db->query_db($sql . "order by SALESPERSON ");

			$row_count = $db->num_rows ( $rows );
			if ($row_count === false || $row_count == 0) { 
				die("No Activity " . $go_back); 
			}

			print_selected($rows, $db, $csvfile);

			$rows = $db->query_db($sql . "order by SALES_ID, invoice.PAID_DATE ");
			print_selected($rows, $db, $csvfile);
	    
			print_report($db, $rows, ++$count);

            		//$db->free_stmt( $stmt );
            		$db->close_connection ();

/**********************************************************************************/
		}
	} else  {
?>
		<span class="title">Commission Report</span>&nbsp; <a href="manage_users.php">Manage Users</a>
<pre>
<form action="commission.php" method="post">
    <label for="test1">Start: </label><input type="text" id="test1" name="start_date"/><br />
    <label for="test2">End:   </label><input type="text" id="test2" name="end_date"  /><br />
	<input type="submit" name="submit_dates" value="Run" />
</form>
</pre>
	<p>Enter dates and press RUN button</p>
	<p><a href="index.php"><img src="images/back_arrow.jpg" class="no-border"  alt="Back Arrow"/>Back</a></p>
<?php
	}

//	if it worked add link, maybe rename or delete after done
	if (isset($count)) {
		if ($count>=2) {
?>
			<p><a href="temp/report.doc">Download Report (Word)</a></p>
			<p><a href="temp/report.csv">Download Report (CSV)</a></p>
<?php
			echo $go_back;
		}
	}

	if(isset($csvfile)) {
		fclose($csvfile);
		unset($csvfile);
	}
?>

</body>
</html>

