<?php
/**
 * Email Verification Utility
 * Handles email confirmation for new accounts
 */

// Generate email verification token
function generate_verification_token($user_id) {
    global $conn;
    
    // Generate unique token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Store token in database
    $sql = "INSERT INTO email_verification (user_id, token, expires_at) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $token, $expires_at]);
    
    return $token;
}

// Send verification email
function send_verification_email($email, $full_name, $token) {
    // Use PHPMailer
    require_once '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once '../vendor/phpmailer/phpmailer/src/Exception.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    
    try {
        // Server settings for MailHog
        $mail->isSMTP();
        $mail->Host = 'localhost';           // MailHog SMTP server
        $mail->SMTPAuth = false;             // MailHog doesn't require auth
        $mail->Port = 1025;                  // MailHog SMTP port
        $mail->SMTPDebug = 0;                // Set to 2 for debugging
        
        // Recipients
        $mail->setFrom('noreply@taskmaster.local', 'Task Master System');
        $mail->addAddress($email, $full_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address - Task Master';
        
        $verification_link = "http://localhost/task_management_system/verify-email.php?token=" . $token;
        
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #f39c12;'>Welcome to Task Master!</h2>
                <p>Hello <strong>$full_name</strong>,</p>
                <p>Thank you for registering with Task Master. To complete your registration, please verify your email address by clicking the button below:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$verification_link' style='background-color: #f39c12; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Verify Email Address</a>
                </div>
                <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
                <p><a href='$verification_link'>$verification_link</a></p>
                <p>This verification link will expire in 24 hours.</p>
                <p>If you didn't create an account with us, you can safely ignore this email.</p>
                <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
                <p style='font-size: 12px; color: #666;'>Best regards,<br>Task Master Team</p>
            </div>
        </body>
        </html>";
        
        $mail->AltBody = "Welcome to Task Master! Please verify your email by visiting: $verification_link";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email verification failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Verify email token
function verify_email_token($token) {
    global $conn;
    
    // Check if token is valid and not expired
    $sql = "SELECT user_id FROM email_verification 
            WHERE token = ? AND expires_at > NOW() AND is_used = FALSE";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$token]);
    $result = $stmt->fetch();
    
    if ($result) {
        $user_id = $result['user_id'];
        
        // Mark user as verified
        $sql = "UPDATE users SET email_verified = TRUE WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        // Mark token as used
        $sql = "UPDATE email_verification SET is_used = TRUE WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$token]);
        
        return $user_id;
    }
    
    return false;
}

// Check if user email is verified
function is_email_verified($user_id) {
    global $conn;
    
    $sql = "SELECT email_verified FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    
    return $result ? $result['email_verified'] : false;
}
?>