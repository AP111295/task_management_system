<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) ) {
?>
<!DOCTYPE html>
<!-- 50:04 -->
<html>
<head>
	<title>Manage Users</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php"; ?>
	<div class="body">
		<?php include "inc/nav.php"; ?>
		<section class="section-1">
			<h4 class="title">Manage Users <a href="add-user.php">Add User</a></h4>
			<table class="main-table">
				<tr>
					<th>#</th>
					<th>Full Name</th>
					<th>Username</th>
					<th>Role</th>
					<th>Action</th>
				</tr>
				    <td>1</td>
				    <td>Elias A</td>
				    <td>elias</td>
				    <td>Employee</td>
				    <td>
					    <a href="edit-user.php?id=1" class="edit-btn">Edit</a>
					    <a href="delete-user.php?id=1" class="delete-btn">Delete</a>
				    </td>
				</tr>
			</table>
		</section>
		<section class="section-2">
			<!-- Additional content can go here -->
		</section>
	</div>

	<script type="text/javascript">
	var active = document.querySelector("#navList li:nth-child(2)");
	active.classList.add("active");
	</script>
</body>
</html>
<?php }else{ 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
 ?>