<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    include "../DB_connection.php";
    
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        
        $sql = "SELECT * FROM tasks WHERE assigned_to = ? ORDER BY due_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($tasks);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>