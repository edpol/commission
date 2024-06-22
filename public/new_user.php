<?php 
	require_once ("../includes/initialize.php"); 
	confirm_logged_in(); 

	$user_set = $mysqlidb->find_all_users();

	if (isset($_POST['submit'])) {
	// Process the form
  
		// validations
		$required_fields = array("username", "password");
		validate_presences($required_fields);
  
		if (empty($errors)) {
			// Perform Create

			$username = $mysqlidb->mysqli_prep($_POST["username"]);
			$hashed_password = password_encrypt($_POST["password"]);
    
			$query  = "INSERT INTO admins (";
			$query .= "  username, hashed_password";
			$query .= ") VALUES (";
			$query .= "  '{$username}', '{$hashed_password}'";
			$query .= ")";
			$result = $mysqlidb->query($query);

			if ($result) {
				// Success
				$_SESSION["message"] = "User created.";
			} else {
				// Failure
				$_SESSION["message"] = "User creation failed.";
			}
			$mysqlidb->close_connection();
			redirect_to("manage_users.php");
		}
	} else {
		// This is probably a GET request
  	} 
	// end: if (isset($_POST['submit']))

	include("head_tag.php");
?>
<body>

	<h2>Add Users</h2>

	<form action="new_user.php" method="post">
		<div style="float:left;">
			<p style="line-height:22px;">Username: </p>
			<p style="line-height:22px;">Password: </p>
			<p>&nbsp;    </p>
		</div>
		<div style="float:left;">
			<p><input type="text"     name="username" value="" /></p>
			<p><input type="password" name="password" value="" /></p>
			<p><input type="submit"   name="submit"   value="Create User" /></p>
		</div>
		<br clear="all" />
	</form>
	<a href="manage_users.php">Cancel</a>

</body>
</html>