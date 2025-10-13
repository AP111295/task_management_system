<?php
session_start();
include "../DB_connection.php";

function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check required fields based on reset type
$required_fields_check = isset($_POST['user_name']) && isset($_POST['new_password']) && isset($_POST['confirm_password']);

if ($reset_type === 'client') {
    $required_fields_check = $required_fields_check && isset($_POST['temp_password']);
}

if ($required_fields_check) {
    
    $user_name = validate_input($_POST['user_name']);
    $temp_password = isset($_POST['temp_password']) ? validate_input($_POST['temp_password']) : '';
    $new_password = validate_input($_POST['new_password']);
    $confirm_password = validate_input($_POST['confirm_password']);
    $reset_type = isset($_POST['reset_type']) ? validate_input($_POST['reset_type']) : 'client';
    $notification_id = isset($_POST['notification_id']) ? validate_input($_POST['notification_id']) : null;
    
    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        $em = "New passwords do not match";
        $redirect_url = ($reset_type === 'admin') ? "../reset-password.php?type=admin&error=$em&username=$user_name" : "../reset-password.php?error=$em&username=$user_name";
        if ($notification_id) {
            $redirect_url .= "&notification_id=$notification_id";
        }
        header("Location: $redirect_url");
        exit();
    }
    
    // Check if user exists
    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_name]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        // Different validation based on reset type
        if ($reset_type === 'admin') {
            // Admin reset: Check if current user is admin
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                $em = "Access denied";
                header("Location: ../login.php?error=$em");
                exit();
            }
            
            // For admin reset, temp_password is optional (can be empty for force reset)
            if (empty($temp_password)) {
                $password_valid = true; // Admin force reset
            } else {
                // Admin provided current password, verify it
                $password_valid = password_verify($temp_password, $user['password']);
            }
            
        } else {
            // Client reset: Verify temporary password normally (required)
            $password_valid = password_verify($temp_password, $user['password']);
        }
        
        if ($password_valid) {
            // Hash new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update with new password
            $sql = "UPDATE users SET password=? WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$hashed_new_password, $user_name]);
            
            // Update notification status if applicable
            if ($notification_id) {
                $sql = "UPDATE notifications SET status='processed' WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$notification_id]);
            }
            
            $em = "Password changed successfully";
            
            // Redirect based on reset type
            if ($reset_type === 'admin') {
                header("Location: ../user.php?success=$em");
            } else {
                header("Location: ../login.php?success=$em");
            }
            exit();
            
        } else {
            $em = "Current password incorrect";
            $redirect_url = ($reset_type === 'admin') ? "../reset-password.php?type=admin&error=$em&username=$user_name" : "../reset-password.php?error=$em&username=$user_name";
            if ($notification_id) {
                $redirect_url .= "&notification_id=$notification_id";
            }
            header("Location: $redirect_url");
            exit();
        }
    } else {
        $em = "User not found";
        $redirect_url = ($reset_type === 'admin') ? "../reset-password.php?type=admin&error=$em" : "../reset-password.php?error=$em";
        header("Location: $redirect_url");
        exit();
    }
    
} else {
    $em = "Please fill all fields";
    $reset_type = isset($_POST['reset_type']) ? validate_input($_POST['reset_type']) : 'client';
    $redirect_url = ($reset_type === 'admin') ? "../reset-password.php?type=admin&error=$em" : "../reset-password.php?error=$em";
    header("Location: $redirect_url");
    exit();
}
?>