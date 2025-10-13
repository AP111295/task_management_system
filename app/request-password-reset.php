<?php
include "../DB_connection.php";
require "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateRandomPassword($length = 8) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

if (isset($_POST['user_name']) && isset($_POST['email'])) {
    $user_name = validate_input($_POST['user_name']);
    $email = validate_input($_POST['email']);
    
    // Check if user exists with this username and email
    $sql = "SELECT * FROM users WHERE username=? AND email=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_name, $email]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        // Générer un mot de passe temporaire
        $temp_password = generateRandomPassword(10);
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
        
        // Mettre à jour le mot de passe dans la base de données
        $sql = "UPDATE users SET password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hashed_password, $user['id']]);
        
        // Envoyer l'email avec PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP pour MailHog
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 1025;
            $mail->SMTPAuth = false;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('taskmaster@company.com', 'TaskMaster Support');
            $mail->addAddress($email, $user['full_name']);

            $mail->Subject = 'Password Reset - Your temporary password';
            $mail->Body = "Hello {$user['full_name']},\n\nYour password has been reset. Here is your temporary password:\n\n$temp_password\n\nPlease log in and change this password immediately for security reasons.\n\nBest regards,\nTaskMaster Support";

            $mail->send();
            
            // Créer une notification pour l'admin (avec les colonnes qui existent)
            try {
                $sql = "INSERT INTO notifications (user_id, username, email, reason, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $user['id'],
                    $user['username'],
                    $email,
                    'password_reset_completed',
                    'processed'
                ]);
            } catch (PDOException $e) {
                // Si cette structure ne fonctionne pas non plus, on ignore la notification
                error_log("Notification insertion failed: " . $e->getMessage());
            }
            
            // Log l'email envoyé
            $log_message = date('Y-m-d H:i:s') . " - Password reset email sent to: $email (User: {$user['full_name']})\n";
            file_put_contents("../email_log.txt", $log_message, FILE_APPEND);
            
            $em = "A temporary password has been sent to your email address. Please check your email and enter the temporary password below.";
            header("Location: ../reset-password.php?success=$em&username=$user_name");
            exit();
            
        } catch (Exception $e) {
            // Log l'erreur
            $error_message = date('Y-m-d H:i:s') . " - Email sending failed for: $email - Error: " . $mail->ErrorInfo . "\n";
            file_put_contents("../email_log.txt", $error_message, FILE_APPEND);
            
            $em = "Email could not be sent. Please contact support.";
            header("Location: ../forgot-password.php?error=$em");
            exit();
        }
        
    } else {
        $em = "Username or email incorrect";
        header("Location: ../forgot-password.php?error=$em");
        exit();
    }
} else {
    $em = "Please fill all fields";
    header("Location: ../forgot-password.php?error=$em");
    exit();
}
?>