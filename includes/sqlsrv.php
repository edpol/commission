<?php
require_once(LIB_PATH.DS."config.php");

class sqlsrv {

	private $connection;
	public  $last_query;

	function __construct($db) {
		$this->open_connection($db);
	}

 	/*
	 *	Connect to database
	 */
	public function open_connection($db) {
		if (!function_exists('sqlsrv_connect')) {
			die("Function sqlsrv_connect does not exist");
		}		
		
		$connectionInfo = array( "Database"=>$db, "UID"=>DB_USER, "PWD"=>DB_PASS, 'ReturnDatesAsStrings'=> true, "CharacterSet" => 'utf-8');

		$this->connection = sqlsrv_connect( DB_SERVER, $connectionInfo);

		if ( $this->connection ) {
			$x = "Connection to {$db} established.<br />";
		} else {
			die("<pre>Database connection failed: " . print_r( sqlsrv_errors(), true));
		}
		return;
	}

	public function query ($sql) {
		$this->last_query = $sql;
		$params = array();
		$options = array( "Scrollable" => "buffered");
		$options = array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
		$result = sqlsrv_query( $this->connection, $sql, $params, $options );
		$this->confirm_query($result);
		return $result;
	}

	private function confirm_query($result) {
		if ($result === false) {
		    $output = "<pre>Database query failed: " . print_r( sqlsrv_errors(), true);
		    $output .= " Last SQL query: " . $this->last_query;
		    die( $output );
		}
	}

	public function fetch_array ($stmt) {
		return sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
	}

	public function num_rows($stmt) {
		return sqlsrv_num_rows($stmt);
	}

	public function free_stmt ( $stmt ) {
		sqlsrv_free_stmt( $stmt );
	}

	public function close_connection() {
		if(isset($this->connection)) {
			sqlsrv_close($this->connection);
			unset($this->connection);
		}
	}
}
