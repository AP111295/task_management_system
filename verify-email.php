<?php
session_start();
include "DB_connection.php";
include "app/email_verification.php";

$message = "";
$message_type = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $user_id = verify_email_token($token);
    
    if ($user_id) {
        $message = "Email verified successfully! You can now log in to your account.";
        $message_type = "success";
    } else {
        $message = "Invalid or expired verification token. Please request a new verification email.";
        $message_type = "error";
    }
} else {
    $message = "No verification token provided.";
    $message_type = "error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .verification-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .verification-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .verification-icon.success {
            color: #27ae60;
        }
        
        .verification-icon.error {
            color: #e74c3c;
        }
        
        .verification-message {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .verification-message.success {
            color: #27ae60;
        }
        
        .verification-message.error {
            color: #e74c3c;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .verification-container {
                margin: 50px 20px;
                padding: 30px 20px;
            }
            
            .verification-icon {
                font-size: 48px;
            }
            
            .verification-message {
                font-size: 16px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-icon <?= $message_type ?>">
            <?php if ($message_type === 'success'): ?>
                <i class="fa fa-check-circle"></i>
            <?php else: ?>
                <i class="fa fa-exclamation-triangle"></i>
            <?php endif; ?>
        </div>
        
        <div class="verification-message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        
        <div class="action-buttons">
            <?php if ($message_type === 'success'): ?>
                <a href="login.php" class="btn btn-primary">
                    <i class="fa fa-sign-in"></i> Go to Login
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary">
                    <i class="fa fa-sign-in"></i> Go to Login
                </a>
                <a href="add-user.php" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Register Again
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>