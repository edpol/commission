<?php 

    require_once ("../includes/initialize.php");

    $dbname = "MOM-Clientele";
    $database = new sqlsrv($dbname);
    $db =& $database;

    $last_ship_date = "2018-09-01";

    $sql  = "select cust.custnum, ctype, cust.firstname, cust.lastname, cust.addr, cust.addr2, cust.city, cust.state, cust.zipcode, cust.country, cust.phone, cust.email, last_ship_date, cust.sales_id ";
    $sql .= "from cust ";
    $sql .= "inner join ";
    $sql .= "( select cms.custnum, max(cms.ship_date) as last_ship_date ";
    $sql .=	"from cms ";
    $sql .= "inner join cust ";
    $sql .= "on cust.custnum=cms.custnum ";
    $sql .= "where cms.ship_date is not null ";
    $sql .= "group by cms.custnum ";
    $sql .= ") as temp ";
    $sql .= "on cust.custnum=temp.custnum ";
    $sql .= "where last_ship_date<'{$last_ship_date}' and cust.phone is not null and len(cust.phone)=14 ";
    $sql .= "and cust.country='001' and ctype in ('r','') ";
    $sql .= "order by last_ship_date desc ";

    //echo $sql . "<br />";
    $rows = $db->query_db($sql);

    $row_count = $db->num_rows ( $stmt );
    $fp = fopen('file.csv', 'w');

    foreach($rows as $row){
        $sql = "select item from items join cms on cms.orderno=items.orderno where cms.custnum=" . $row['custnum'] . " group by item ";
        //print_r($row);
        $line = array();
        foreach($row as $key=>$value) {
            $line[$key] = trim($value);
            $rows2 = $db->query_db($sql);
            $items = array();
            foreach($rows2 as $row2) {
                $items[] = trim($row2['item']);
            }
        }
        $new_line = array_merge($line, $items);
        fputcsv($fp, $new_line);
        //print_r($line);
    }
    fclose($fp);

echo "Row Count: $row_count";
