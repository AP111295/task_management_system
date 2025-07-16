<?php 
// Start the session
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) ) {
// User is logged in, proceed with the page
if (isset($_POST['user_name']) && isset($_POST['password']) && isset($_POST['full_name'])) {
    include "../DB_connection.php";

    function validate_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $user_name = validate_input($_POST['user_name']);
	$password = validate_input($_POST['password']);
	$full_name = validate_input($_POST['full_name']);

   // Check if fields are empty
   if (empty($user_name)) {
		$em = "User name is required";
	    header("Location: ../add-user.php?error=$em");
	    exit();
	}else if (empty($password)) {
		$em = "Password is required";
	    header("Location: ../add-user.php?error=$em");
	    exit();
	}else if (empty($full_name)) {
		$em = "Full name is required";
	    header("Location: ../add-user.php?error=$em");
	    exit();
    } else {
        // Check if user already exists
        include "Model/User.php";
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $data = array($full_name, $user_name, $password, "employee"); // Assuming role is 'employee'
        insert_user($conn, $data);

        $em = "User created successfully";
	    header("Location: ../add-user.php?success=$em");
	    exit();
	}
}else {
   $em = "Unknown error occurred";
   header("Location: ../add-user.php?error=$em");
   exit();
}
}else{ 
   $em = "First login";
   header("Location: ../add-user.php?error=$em");
   exit();
}