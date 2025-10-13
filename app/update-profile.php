<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    include "csrf_protection.php";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Verify CSRF token
        verify_csrf_token();
        
        $user_id = $_SESSION['id'];
        
        // Handle profile picture upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] != UPLOAD_ERR_NO_FILE) {
            
            // Check for upload errors
            if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
                header("Location: ../profile.php?error=File upload error");
                exit();
            }
            
            $file = $_FILES['profile_image'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];
            
            // Get file extension
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            // Validate file
            if (!in_array($file_ext, $allowed_extensions)) {
                header("Location: ../profile.php?error=Invalid file type. Only JPG, JPEG, PNG and GIF are allowed");
                exit();
            }
            
            if ($file_size > 5000000) { // 5MB limit
                header("Location: ../profile.php?error=File size too large. Maximum 5MB allowed");
                exit();
            }
            
            // Create uploads directory if it doesn't exist
            $upload_dir = "../uploads/profiles/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $new_file_name = $user_id . "_" . uniqid() . "." . $file_ext;
            $file_destination = $upload_dir . $new_file_name;
            
            // Get current profile image to delete old one
            $get_current_sql = "SELECT profile_image FROM users WHERE id = ?";
            $get_current_stmt = $conn->prepare($get_current_sql);
            $get_current_stmt->execute([$user_id]);
            $current_user = $get_current_stmt->fetch();
            
            if (move_uploaded_file($file_tmp, $file_destination)) {
                // Delete old profile image if exists
                if (!empty($current_user['profile_image']) && file_exists($upload_dir . $current_user['profile_image'])) {
                    unlink($upload_dir . $current_user['profile_image']);
                }
                
                // Update database with new profile image
                $avatar_color = isset($_POST['avatar_color']) ? $_POST['avatar_color'] : '#f39c12';
                $sql = "UPDATE users SET profile_image = ?, avatar_color = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                
                if ($stmt->execute([$new_file_name, $avatar_color, $user_id])) {
                    $_SESSION['profile_image'] = $new_file_name;
                    $_SESSION['avatar_color'] = $avatar_color;
                    header("Location: ../profile.php?success=Profile picture updated successfully!");
                    exit();
                } else {
                    header("Location: ../profile.php?error=Failed to update profile picture in database");
                    exit();
                }
            } else {
                header("Location: ../profile.php?error=Failed to upload file");
                exit();
            }
        }
        
        // Handle profile picture removal
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            // Get current profile image
            $get_current_sql = "SELECT profile_image FROM users WHERE id = ?";
            $get_current_stmt = $conn->prepare($get_current_sql);
            $get_current_stmt->execute([$user_id]);
            $current_user = $get_current_stmt->fetch();
            
            // Delete file if exists
            $upload_dir = "../uploads/profiles/";
            if (!empty($current_user['profile_image']) && file_exists($upload_dir . $current_user['profile_image'])) {
                unlink($upload_dir . $current_user['profile_image']);
            }
            
            // Update database to remove profile image
            $sql = "UPDATE users SET profile_image = NULL WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$user_id])) {
                $_SESSION['profile_image'] = null;
                header("Location: ../profile.php?success=Profile picture removed successfully!");
                exit();
            } else {
                header("Location: ../profile.php?error=Failed to remove profile picture");
                exit();
            }
        }
        
        // Handle avatar color update only
        if (isset($_POST['avatar_color']) && !isset($_FILES['profile_image'])) {
            $avatar_color = $_POST['avatar_color'];
            
            $sql = "UPDATE users SET avatar_color = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$avatar_color, $user_id])) {
                $_SESSION['avatar_color'] = $avatar_color;
                header("Location: ../profile.php?success=Avatar color updated successfully!");
                exit();
            } else {
                header("Location: ../profile.php?error=Failed to update avatar color");
                exit();
            }
        }
        
        // Handle regular profile information update
        if (isset($_POST['full_name']) && isset($_POST['email']) && isset($_POST['username'])) {
            $full_name = trim($_POST['full_name']);
            $email = trim($_POST['email']);
            $username = trim($_POST['username']);
            
            // Validate inputs
            if (empty($full_name) || empty($email) || empty($username)) {
                header("Location: ../profile.php?error=All fields are required");
                exit();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../profile.php?error=Invalid email format");
                exit();
            }
            
            // Check if username or email already exists (excluding current user)
            $check_sql = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute([$username, $email, $user_id]);
            
            if ($check_stmt->rowCount() > 0) {
                header("Location: ../profile.php?error=Username or email already exists");
                exit();
            }
            
            // Update user information
            $sql = "UPDATE users SET full_name = ?, email = ?, username = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$full_name, $email, $username, $user_id])) {
                // Update session data
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $username;
                
                header("Location: ../profile.php?success=Profile updated successfully!");
                exit();
            } else {
                header("Location: ../profile.php?error=Failed to update profile");
                exit();
            }
        }
        
        header("Location: ../profile.php?error=No valid data submitted");
        exit();
        
    } else {
        header("Location: ../profile.php?error=Invalid request method");
        exit();
    }
} else {
    header("Location: ../login.php?error=Please login first");
    exit();
}
?>