<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    include "../DB_connection.php";
    
    if (isset($_POST['task_id'])) {
        $task_id = $_POST['task_id'];
        
        try {
            $sql = "DELETE FROM tasks WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$task_id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Task not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Task ID required']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
}
?>