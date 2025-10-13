<?php
session_start();
include "../DB_connection.php";
include "csrf_protection.php";
include "brute_force_protection.php";

// Function to clean and secure user input
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if all required fields are submitted
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['captcha_answer'])) {
    
    // Verify CSRF token
    verify_csrf_token();
    
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);
    $captcha_answer = validate_input($_POST['captcha_answer']);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Clean old login attempts
    clean_old_attempts();
    
    // Check if account is locked
    if (is_account_locked($username, $ip_address)) {
        $remaining_time = get_lockout_time_remaining($username, $ip_address);
        $minutes = ceil($remaining_time / 60);
        $em = "Account temporarily locked due to multiple failed login attempts. Please try again in $minutes minute(s).";
        header("Location: ../login.php?error=$em");
        exit();
    }

    // Check if username is provided
    if (empty($username)) {
        $em = "Username is required";
        header("Location: ../login.php?error=$em");
        exit();
    } 
    // Check if password is provided
    else if (empty($password)) {
        $em = "Password is required";
        header("Location: ../login.php?error=$em");
        exit();
    } 
    // Check if security question is answered
    else if (empty($captcha_answer)) {
        $em = "Please solve the security question";
        header("Location: ../login.php?error=$em");
        exit();
    } 
    // Check if security question answer is correct
    else if (!isset($_SESSION['captcha_answer']) || intval($captcha_answer) !== $_SESSION['captcha_answer']) {
        $em = "Security question answer is incorrect";
        // Clear captcha data after error
        unset($_SESSION['captcha_num1']);
        unset($_SESSION['captcha_num2']);
        unset($_SESSION['captcha_operation']);
        unset($_SESSION['captcha_answer']);
        header("Location: ../login.php?error=$em");
        exit();
    } else {
        try {
            // Look for user in database
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username]);

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                
                // Check if password matches
                if (password_verify($password, $user['password'])) {
                    // Login successful - clear captcha data
                    unset($_SESSION['captcha_num1']);
                    unset($_SESSION['captcha_num2']);
                    unset($_SESSION['captcha_operation']);
                    unset($_SESSION['captcha_answer']);
                    
                    // Clear login attempts for successful login
                    clear_login_attempts($username, $ip_address);
                    
                    // Store user information in session
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['profile_image'] = $user['profile_image'];
                    $_SESSION['avatar_letter'] = $user['avatar_letter'];
                    $_SESSION['avatar_color'] = $user['avatar_color'];
                    
                    // Redirect to main page
                    header("Location: ../index.php");
                    exit();
                } else {
                    // Track failed login attempt
                    track_failed_login($username, $ip_address);
                    
                    $em = "Incorrect username or password";
                    // Clear captcha data after error
                    unset($_SESSION['captcha_num1']);
                    unset($_SESSION['captcha_num2']);
                    unset($_SESSION['captcha_operation']);
                    unset($_SESSION['captcha_answer']);
                    header("Location: ../login.php?error=$em");
                    exit();
                }
            } else {
                // Track failed login attempt
                track_failed_login($username, $ip_address);
                
                $em = "Incorrect username or password";
                // Clear captcha data after error
                unset($_SESSION['captcha_num1']);
                unset($_SESSION['captcha_num2']);
                unset($_SESSION['captcha_operation']);
                unset($_SESSION['captcha_answer']);
                header("Location: ../login.php?error=$em");
                exit();
            }
        } catch (PDOException $e) {
            $em = "Database connection error";
            header("Location: ../login.php?error=$em");
            exit();
        }
    }
} else {
    $em = "Please fill all fields";
    header("Location: ../login.php?error=$em");
    exit();
}
?>