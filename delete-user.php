<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) ) {
include "DB_connection.php";
	include "app/Model/User.php";

    if(!isset($_GET['id'])) {
       header("Location: user.php");
       exit();
    }
    $id = $_GET['id'];
	$user = get_user_by_id($conn, $id);
	if ($user == 0) {
		header(("location: user.php"));
		exit();
	}
    $data = array($id);
    
    // Check if user has tasks assigned
    $check_sql = "SELECT COUNT(*) FROM tasks WHERE assigned_to = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$id]);
    $task_count = $check_stmt->fetchColumn();
    
    // Delete the user and get the number of affected rows
    $deleted_rows = delete_user($conn, $data);
    
    if ($deleted_rows > 0) {
        if ($task_count > 0) {
            $sm = "User and their $task_count assigned task(s) deleted successfully";
        } else {
            $sm = "User deleted successfully";
        }
    } else {
        $em = "Failed to delete user. User may not exist.";
        header("location: user.php?error=$em");
        exit();
    }
    header("location: user.php?success=$sm");
    exit();

 }else{ 
   $em = "First login"; 
   header("Location: login.php?error=$em");
   exit();
}
 ?>