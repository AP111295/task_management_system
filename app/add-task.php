<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    include "../DB_connection.php";
    
    function validate_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    if (isset($_POST['task_name']) && isset($_POST['assigned_to']) && isset($_POST['due_date'])) {
        $task_name = validate_input($_POST['task_name']);
        $description = validate_input($_POST['description']);
        $assigned_to = validate_input($_POST['assigned_to']);
        $due_date = validate_input($_POST['due_date']);
        
        if (empty($task_name)) {
            $em = "Task name is required";
            header("Location: ../index.php?error=$em");
            exit();
        } else if (empty($due_date)) {
            $em = "Deadline is required";
            header("Location: ../index.php?error=$em");
            exit();
        } else {
            // Insert new task avec seulement les colonnes qui existent
            $sql = "INSERT INTO tasks (title, description, assigned_to, due_date, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$task_name, $description, $assigned_to, $due_date]);
            
            $em = "Task assigned successfully to employee!";
            header("Location: ../index.php?success=$em");
            exit();
        }
    } else {
        $em = "Please fill all required fields";
        header("Location: ../index.php?error=$em");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>