<?php 
	require_once ("../includes/initialize.php"); 
	confirm_logged_in(); 

	$user = $mysqlidb->find_user_by_id($_GET["id"]);

	if (!$user) {
		// admin ID was missing or invalid or 
		// admin couldn't be found in database
		redirect_to("manage_users.php");
	}

	$id = $user["id"];
	$query = "DELETE FROM admins WHERE id = {$id} LIMIT 1";
	$result = $mysqlidb->query($query);

	if ($result && $mysqlidb->affected_rows() == 1) {
		// Success
		$_SESSION["message"] = "User deleted.";
	} else {
		// Failure
		$_SESSION["message"] = "User deletion failed.";
	}
	$mysqlidb->close_connection();
	redirect_to("manage_users.php");
?>