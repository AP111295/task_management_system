<?php
session_start();
include "../DB_connection.php";

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=Access denied");
    exit();
}

function generate_temp_password($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $temp_password = '';
    for ($i = 0; $i < $length; $i++) {
        $temp_password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $temp_password;
}

function send_temp_password_email($email, $username, $temp_password) {
    // For now, we'll just add to email log
    $log_message = "=== TEMPORARY PASSWORD EMAIL ===\n";
    $log_message .= "To: $email\n";
    $log_message .= "Username: $username\n";
    $log_message .= "Temporary Password: $temp_password\n";
    $log_message .= "Please use this temporary password to login, then change it from your profile.\n";
    $log_message .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $log_message .= "================================\n\n";
    
    file_put_contents('../email_log.txt', $log_message, FILE_APPEND);
    return true;
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    // Get user info
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        // Generate temporary password
        $temp_password = generate_temp_password();
        $hashed_temp_password = password_hash($temp_password, PASSWORD_DEFAULT);
        
        // Update user password with temporary password
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hashed_temp_password, $user_id]);
        
        // Send email (log for now)
        if (send_temp_password_email($user['email'], $user['username'], $temp_password)) {
            $success = "Temporary password generated and sent to " . $user['email'];
            header("Location: ../user.php?success=" . urlencode($success));
        } else {
            $error = "Failed to send email";
            header("Location: ../user.php?error=" . urlencode($error));
        }
    } else {
        $error = "User not found";
        header("Location: ../user.php?error=" . urlencode($error));
    }
} elseif (isset($_GET['username'])) {
    $username = $_GET['username'];
    
    // Get user info by username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        // Generate temporary password
        $temp_password = generate_temp_password();
        $hashed_temp_password = password_hash($temp_password, PASSWORD_DEFAULT);
        
        // Update user password with temporary password
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hashed_temp_password, $user['id']]);
        
        // Send email (log for now)
        if (send_temp_password_email($user['email'], $user['username'], $temp_password)) {
            $success = "Temporary password generated and sent to " . $user['email'];
            header("Location: ../user.php?success=" . urlencode($success));
        } else {
            $error = "Failed to send email";
            header("Location: ../user.php?error=" . urlencode($error));
        }
    } else {
        $error = "User not found";
        header("Location: ../user.php?error=" . urlencode($error));
    }
} else {
    $error = "Invalid request";
    header("Location: ../user.php?error=" . urlencode($error));
}
?>