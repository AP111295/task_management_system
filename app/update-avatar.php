<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['avatar_letter']) && isset($_POST['avatar_color'])) {
        $user_id = $_SESSION['id'];
        $avatar_letter = $_POST['avatar_letter'];
        $avatar_color = $_POST['avatar_color'];
        
        // Validate inputs
        if (strlen($avatar_letter) != 1 || !ctype_alpha($avatar_letter)) {
            echo json_encode(['success' => false, 'message' => 'Invalid avatar letter']);
            exit();
        }
        
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $avatar_color)) {
            echo json_encode(['success' => false, 'message' => 'Invalid color format']);
            exit();
        }
        
        // Delete old profile image if exists
        $old_image_query = "SELECT profile_image FROM users WHERE id = ?";
        $old_image_stmt = $conn->prepare($old_image_query);
        $old_image_stmt->execute([$user_id]);
        $old_image = $old_image_stmt->fetchColumn();
        
        if ($old_image && file_exists("../uploads/profiles/" . $old_image)) {
            unlink("../uploads/profiles/" . $old_image);
        }
        
        // Update database
        $sql = "UPDATE users SET profile_image = NULL, avatar_letter = ?, avatar_color = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$avatar_letter, $avatar_color, $user_id])) {
            // Update session variables
            $_SESSION['profile_image'] = null;
            $_SESSION['avatar_letter'] = $avatar_letter;
            $_SESSION['avatar_color'] = $avatar_color;
            
            echo json_encode(['success' => true, 'message' => 'Avatar updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update avatar']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
}
?>