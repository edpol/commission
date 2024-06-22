<?php

defined('DBi_SERVER') ? null : define("DBi_SERVER", "localhost");
defined('DBi_USER')   ? null : define("DBi_USER", "payroll");
defined('DBi_PASS')   ? null : define("DBi_PASS", "Mdr33325");
defined('DBi_NAME')   ? null : define("DBi_NAME", "commission");

class MySQLDatabase {

	private $my_conn;
	public  $last_query;
	private $magic_quotes_active;
	private $real_escape_string_exists;

	public function find_user_by_username($username) {		
		$safe_username = $this->escape_value($username);
		$query  = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "WHERE username = '{$safe_username}' ";
		$query .= "LIMIT 1";
		$user_set = $this->query($query);
		if($user = mysqli_fetch_assoc($user_set)) {
			return $user;
		} else {
			return null;
		}
	}

	function __construct() {
		$this->open_connection(); 
		$this->magic_quotes_active = get_magic_quotes_gpc();
		$this->real_escape_string_exists = function_exists( "mysqli_real_escape_string" );
	}

	public function open_connection() {
/*	echo "Server ".DBi_SERVER."<br />User ".DBi_USER."<br />Password ".DBi_PASS."<br />Database ".DBi_NAME."<br />"; */
		$this->my_conn = mysqli_connect(DBi_SERVER,DBi_USER,DBi_PASS,DBi_NAME);
		if (!$this->my_conn) {
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
	}

	public function close_connection() {
		if(isset($this->my_conn)) {
			mysqli_close($this->my_conn);
			unset($this->my_conn);
		}
	}

	public function mysqli_prep($string) {
	    return mysqli_real_escape_string($this->my_conn, $string);
	}

	public function query($sql) {
		$this->last_query = $sql;
		$result = mysqli_query($this->my_conn, $sql);
		if(!$result){
            die("Error description: " . mysqli_error($this->my_conn));
        }
		return $result;
	}

	public function affected_rows() {
		return mysqli_affected_rows($this->my_conn);
	}

	public function find_all_users() {
		$query  = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "ORDER BY username ASC";
		return $this->query($query);
	}

	public function find_user_by_id($id) {
		$query  = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "WHERE id = '{$id}' ";
		$query .= "LIMIT 1";
		$user_set = $this->query($query);
		if($user = mysqli_fetch_assoc($user_set)) {
			return $user;
		} else {
			return null;
		}
	}

	public function escape_value( $value ) {
		if( $this->real_escape_string_exists ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysqli_real_escape_string( $this->my_conn, $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}

	public function attempt_login($username, $password) {
		$user = $this->find_user_by_username($username);
		if ($user) {
			// found user, now check password
			if (password_check($password, $user["hashed_password"])) {
				// password matches
				return $user;
			} else {
				// password does not match
				return false;
			}
		} else {
			// user not found
			return false;
		}
	}
}
$mysqlidb = new MySQLDatabase();
