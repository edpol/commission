<?php 
	require_once ("../includes/initialize.php"); 
	confirm_logged_in(); 
	$user_set = $mysqlidb->find_all_users();
	include("head_tag.php");
?>
<body>

<h2>Manage Users</h2>

	<table>
		<tr>
			<th style="text-align: left; width: 170px;">Username</th>
			<th colspan="2" style="text-align: left;">Actions</th>
		</tr>
		<?php while($user = mysqli_fetch_assoc($user_set)) { ?>
		<tr>
			<td><?php echo htmlentities($user["username"]); ?></td>
			<td><a href="edit_user.php?id=<?php echo urlencode($user["id"]); ?>">Edit</a>&nbsp;</td>
			<td><a href="delete_user.php?id=<?php echo urlencode($user["id"]); ?>" onClick="return confirm('Are you sure?');">Delete</a></td>
		</tr>
		<?php } ?>
	</table>
	<p><a href="new_user.php">Add new user</a></p>
	<p><a href="commission.php"><img src="images/back_arrow.jpg" border="0"/>Back</a></p>

<?php 
	echo message(); 
//	$mysqlidb->close_connection();
?>
</body>
</html>