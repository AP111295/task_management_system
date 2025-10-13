<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    
    // Check if form was submitted with image file
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
        $user_id = $_SESSION['id'];
        $file = $_FILES['profile_image'];
        
        // Set allowed file types and size limit
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB in bytes
        
        // Check if file type is allowed
        if (!in_array($file['type'], $allowed_types)) {
            header("Location: ../profile.php?error=Invalid file type. Only JPG, JPEG, and PNG are allowed.");
            exit();
        }
        
        // Check if file size is within limit
        if ($file['size'] > $max_size) {
            header("Location: ../profile.php?error=File too large. Maximum size is 2MB.");
            exit();
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = "../uploads/profiles/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Create unique filename to avoid conflicts
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $user_id . '_' . time() . '.' . $file_extension;
        $filepath = $upload_dir . $filename;
        
        // Find and delete old profile image if it exists
        $old_image_query = "SELECT profile_image FROM users WHERE id = ?";
        $old_image_stmt = $conn->prepare($old_image_query);
        $old_image_stmt->execute([$user_id]);
        $old_image = $old_image_stmt->fetchColumn();
        
        if ($old_image && file_exists($upload_dir . $old_image)) {
            unlink($upload_dir . $old_image);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $sql = "UPDATE users SET profile_image = ?, avatar_letter = NULL, avatar_color = NULL WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$filename, $user_id])) {
                // Update session variables
                $_SESSION['profile_image'] = $filename;
                $_SESSION['avatar_letter'] = null;
                $_SESSION['avatar_color'] = null;
                
                header("Location: ../profile.php?success=Profile image updated successfully!");
                exit();
            } else {
                // Delete uploaded file if database update fails
                unlink($filepath);
                header("Location: ../profile.php?error=Failed to update profile image in database.");
                exit();
            }
        } else {
            header("Location: ../profile.php?error=Failed to upload image.");
            exit();
        }
    } else {
        header("Location: ../profile.php?error=No image file received.");
        exit();
    }
} else {
    header("Location: ../login.php?error=Please login first");
    exit();
}
?>