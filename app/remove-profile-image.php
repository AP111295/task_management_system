<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = $_SESSION['id'];
        
        // Get current profile image
        $image_query = "SELECT profile_image FROM users WHERE id = ?";
        $image_stmt = $conn->prepare($image_query);
        $image_stmt->execute([$user_id]);
        $profile_image = $image_stmt->fetchColumn();
        
        // Delete image file if exists
        if ($profile_image && file_exists("../uploads/profiles/" . $profile_image)) {
            unlink("../uploads/profiles/" . $profile_image);
        }
        
        // Update database to remove image and set default avatar
        $user_name_query = "SELECT full_name FROM users WHERE id = ?";
        $user_name_stmt = $conn->prepare($user_name_query);
        $user_name_stmt->execute([$user_id]);
        $full_name = $user_name_stmt->fetchColumn();
        
        $default_letter = strtoupper(substr($full_name, 0, 1));
        $default_color = '#667eea';
        
        $sql = "UPDATE users SET profile_image = NULL, avatar_letter = ?, avatar_color = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$default_letter, $default_color, $user_id])) {
            // Update session variables
            $_SESSION['profile_image'] = null;
            $_SESSION['avatar_letter'] = $default_letter;
            $_SESSION['avatar_color'] = $default_color;
            
            header("Location: ../profile.php?success=Profile picture removed successfully!");
            exit();
        } else {
            header("Location: ../profile.php?error=Failed to remove profile picture");
            exit();
        }
    } else {
        header("Location: ../profile.php?error=Invalid request method");
        exit();
    }
} else {
    header("Location: ../login.php?error=Please login first");
    exit();
}
?>