<?php 
if (isset($_POST['username'])) {
	echo '$_POST["username"] ' . $_POST['username'] . '<br />';
	print_r ($_POST);
}
	require_once ('../includes/initialize.php');

	//if (isset($_POST['submit'])) {echo 'SET <br />';} else {echo 'NOT set <br />';}


	$username = '';
	if (isset($_POST['submit'])) {
		// Process the form
		// validations
		$required_fields = array('username', 'password');
		foreach($required_fields as $field) {
		    $value = trim($_POST[$field]);
			if (isset($value) && $value === '') {
				$errors[$field] = "{$field}  can't be blank"; // bad
			}
		}
  
		if (empty($errors)) {
			// Attempt Login
			//do errors get destroyed
			$username = $_POST['username'];
			$password = $_POST['password'];
			$found_user = $mysqlidb->attempt_login($username, $password);

			if (isset($found_user)) {
				// Success
				// Mark user as logged in
				$_SESSION['user_id']  = $found_user['id'];
				$_SESSION['username'] = $found_user['username'];
				redirect_to('commission.php');
			} else {
				// Failure
				echo 'Username/password not found.';
			}
			$mysqlidb->close_connection();
		} else {
			print_r($errors);
		}

	} else {
		// This is probably a GET request
  	}	// end: if (isset($_POST['submit']))


	include('head_tag.php');
?>
<body>
<!--	<div align="center" id="main"> -->
    <br />
	<span class="title">Login</span>
    <pre>
        <form action="index.php" method="post">
            <label for="username">Username: </label><input type="text"     name="username" id="username" value="<?php echo htmlentities($username); ?>" /><br />
            <label for="password">Password: </label><input type="password" name="password" id="password" value="" /><br />
            <input type="submit"   name="submit"   value="submit" /><br />
        </form>
    </pre>
<!--	</div> -->
</body>
</html>
