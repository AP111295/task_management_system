<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

    if (isset($_POST['user_name']) && isset($_POST['full_name']) && isset($_POST['email']) && $_SESSION['role'] == 'admin') {
        include "../DB_connection.php";

        function validate_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $user_name = validate_input($_POST['user_name']);
        $full_name = validate_input($_POST['full_name']);
        $email = validate_input($_POST['email']);
        $id = validate_input($_POST['id']);

        if (empty($user_name)) {
            $em = "User name is required";
            header("Location: ../edit-user.php?error=$em&id=$id");
            exit();
        } else if (empty($full_name)) {
            $em = "Full name is required";
            header("Location: ../edit-user.php?error=$em&id=$id");
            exit();
        } else if (empty($email)) {
            $em = "Email is required";
            header("Location: ../edit-user.php?error=$em&id=$id");
            exit();
        } else {

            include "Model/User.php";

            // Update user without changing password
            $sql = "UPDATE users SET full_name=?, email=?, username=? WHERE id=? AND role=?";
            $stmt = $conn->prepare($sql);
            $data = array($full_name, $email, $user_name, $id, "employee");
            $stmt->execute($data);

            // Mark notification as processed if present
            if (isset($_POST['notification_id'])) {
                $notification_id = validate_input($_POST['notification_id']);
                $sql = "UPDATE notifications SET status='processed' WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$notification_id]);
                
                $em = "User updated successfully and notification processed";
                header("Location: ../notifications.php?success=$em");
                exit();
            } else {
                $em = "User updated successfully";
                header("Location: ../user.php?success=$em");
                exit();
            }
        }
    } else {
        $em = "Unknown error occurred";
        header("Location: ../user.php?error=$em");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>