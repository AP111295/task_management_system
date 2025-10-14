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
    
    // Check if this is an AJAX request
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    if (isset($_POST['task_name']) && isset($_POST['assigned_to']) && isset($_POST['due_date'])) {
        $task_name = validate_input($_POST['task_name']);
        $description = validate_input($_POST['description']);
        $assigned_to = validate_input($_POST['assigned_to']);
        $due_date = validate_input($_POST['due_date']);
        
        if (empty($task_name)) {
            $em = "Task name is required";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $em]);
                exit();
            } else {
                header("Location: ../index.php?error=$em");
                exit();
            }
        } else if (empty($due_date)) {
            $em = "Deadline is required";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $em]);
                exit();
            } else {
                header("Location: ../index.php?error=$em");
                exit();
            }
        } else {
            try {
                // Insert new task
                $sql = "INSERT INTO tasks (title, description, assigned_to, due_date, status) VALUES (?, ?, ?, ?, 'pending')";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$task_name, $description, $assigned_to, $due_date]);
                
                $task_id = $conn->lastInsertId();
                
                if ($isAjax) {
                    // Return JSON response for AJAX requests
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Task assigned successfully!',
                        'task' => [
                            'id' => $task_id,
                            'title' => $task_name,
                            'description' => $description,
                            'due_date' => $due_date,
                            'status' => 'pending'
                        ]
                    ]);
                    exit();
                } else {
                    $em = "Task assigned successfully to employee!";
                    header("Location: ../index.php?success=$em");
                    exit();
                }
            } catch (Exception $e) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                    exit();
                } else {
                    header("Location: ../index.php?error=Database error occurred");
                    exit();
                }
            }
        }
    } else {
        $em = "Please fill all required fields";
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $em]);
            exit();
        } else {
            header("Location: ../index.php?error=$em");
            exit();
        }
    }
} else {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    } else {
        header("Location: ../login.php");
        exit();
    }
}
?>