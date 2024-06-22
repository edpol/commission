<?php 
	require_once ("../includes/initialize.php"); 
	confirm_logged_in(); 

	$user = $mysqlidb->find_user_by_id($_GET["id"]);

	if (isset($_POST['submit'])) {
		// Process the form
  
		// validations
		$required_fields = array("username", "password");
		validate_presences($required_fields);
    
		if (empty($errors)) {

			// Perform Update

			$id = $user["id"];
			$username = $mysqlidb->mysqli_prep($_POST["username"]);
			$hashed_password = password_encrypt($_POST["password"]);

			$query  = "UPDATE admins SET ";
			$query .= "username = '{$username}', ";
			$query .= "hashed_password = '{$hashed_password}' ";
			$query .= "WHERE id = {$id} ";
			$query .= "LIMIT 1";
			$result = $mysqlidb->query($query);

			if ($result && $mysqlidb->affected_rows() == 1) {
				// Success
				$_SESSION["message"] = "User updated.";
			} else {
				// Failure
				$_SESSION["message"] = "User update failed.";
			}
			$mysqlidb->close_connection();

			redirect_to("manage_users.php");
  		}
	} else {
		// This is probably a GET request  
	}	// end: if (isset($_POST['submit']))

	include("head_tag.php");
?>
<body>

<h2>Edit User: <?php echo $user["username"]; ?></h2>

	<form action="edit_user.php?id=<?php echo urlencode($user["id"]); ?>" method="post">
	<table>
		<tr>
			<th>Username:</th>
			<td><input type="text" name="username" value="<?php echo htmlentities($user["username"]); ?>" /></td>
		</tr>
		<tr>
			<th>Password:</th>
			<td><input type="password" name="password" value="" /></td>
		</tr>
		<tr>
			<th> </th>
			<td><input type="submit" name="submit" value="Edit User" /></td>
		</tr>
	</table>
	</form>
	<a href="manage_users.php">Cancel</a>
</body>
</html>