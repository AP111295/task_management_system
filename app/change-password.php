<?php
session_start();
include "csrf_protection.php";

// Check if user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php?error=Please login first");
    exit();
}

// Include database connection
include "../DB_connection.php";

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Verify CSRF token
    verify_csrf_token();
    
    // Get form data
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_id = $_SESSION['id'];
    
    // Validation
    $errors = [];
    
    // Check if all fields are filled
    if (empty($current_password)) {
        $errors[] = "Current password is required";
    }
    
    if (empty($new_password)) {
        $errors[] = "New password is required";
    }
    
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your new password";
    }
    
    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match";
    }
    
    // Check password length
    if (strlen($new_password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    // Check password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $new_password)) {
        $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, and one number";
    }
    
    // Check if new password is different from current
    if ($new_password === $current_password) {
        $errors[] = "New password must be different from current password";
    }
    
    // If no validation errors, proceed
    if (empty($errors)) {
        
        try {
            // Get current user data from database
            $sql = "SELECT password FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Verify current password
                if (password_verify($current_password, $user['password'])) {
                    
                    // Hash new password
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update password in database
                    $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    
                    if ($update_stmt->execute([$hashed_new_password, $user_id])) {
                        // Password updated successfully
                        
                        // Log the password change
                        $log_message = date('Y-m-d H:i:s') . " - Password changed for user ID: " . $user_id . 
                                      " (Email: " . ($_SESSION['email'] ?? 'N/A') . ")" . 
                                      " (IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . ")\n";
                        file_put_contents('../password_change_log.txt', $log_message, FILE_APPEND | LOCK_EX);
                        
                        // Redirect with success message
                        header("Location: ../profile.php?success=Password changed successfully!");
                        exit();
                        
                    } else {
                        header("Location: ../profile.php?error=Database error: Could not update password");
                        exit();
                    }
                    
                } else {
                    header("Location: ../profile.php?error=Current password is incorrect");
                    exit();
                }
                
            } else {
                header("Location: ../profile.php?error=User not found");
                exit();
            }
            
        } catch (PDOException $e) {
            // Log the error
            error_log("Password change error: " . $e->getMessage());
            header("Location: ../profile.php?error=Database error occurred");
            exit();
        }
        
    } else {
        // Validation errors
        $error_message = implode(", ", $errors);
        header("Location: ../profile.php?error=" . urlencode($error_message));
        exit();
    }
    
} else {
    header("Location: ../profile.php?error=Invalid request method");
    exit();
}
?>