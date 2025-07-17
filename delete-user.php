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
    $data = array($id, "employee");
    delete_user($conn, $data);
    $sm = "User deleted successfully";
    header("location: user.php?success=$sm");
    exit();

 }else{ 
   $em = "First login"; 
   header("Location: login.php?error=$em");
   exit();
}
 ?>