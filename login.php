<?php
session_start();

// Include CSRF protection
include "app/csrf_protection.php";

// Create security question (captcha) - simple math problem
if (!isset($_SESSION['captcha_num1']) || !isset($_SESSION['captcha_num2'])) {
    $_SESSION['captcha_num1'] = rand(1, 10);
    $_SESSION['captcha_num2'] = rand(1, 10);
    $_SESSION['captcha_operation'] = rand(0, 1); // 0 means addition, 1 means subtraction
}

// Refresh captcha if requested
if (isset($_GET['refresh_captcha'])) {
    $_SESSION['captcha_num1'] = rand(1, 10);
    $_SESSION['captcha_num2'] = rand(1, 10);
    $_SESSION['captcha_operation'] = rand(0, 1);
    header('Location: login.php');
    exit();
}

$captcha_question = '';
$captcha_answer = 0;

// Create addition or subtraction question
if ($_SESSION['captcha_operation'] == 0) {
    $captcha_question = $_SESSION['captcha_num1'] . ' + ' . $_SESSION['captcha_num2'] . ' = ?';
    $captcha_answer = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];
} else {
    // Make sure result is always positive
    $num1 = max($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
    $num2 = min($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
    $captcha_question = $num1 . ' - ' . $num2 . ' = ?';
    $captcha_answer = $num1 - $num2;
}

// Store correct answer in session
$_SESSION['captcha_answer'] = $captcha_answer;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
    <style>
        /* LOGIN PAGE RESPONSIVE STYLES */
        .login-page-body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            max-width: 900px;
            width: 100%;
            min-height: 500px;
        }
        
        .login-left, .login-right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .login-right {
            background: white;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 28px;
            font-weight: 700;
        }
        
        .form-title i {
            color: #667eea;
            margin-right: 10px;
        }
        
        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 768px) {
            .login-page-body {
                padding: 10px;
            }
            
            .login-container {
                flex-direction: column;
                max-width: 400px;
                border-radius: 15px;
            }
            
            .login-left {
                padding: 30px 20px;
                min-height: auto;
            }
            
            .login-right {
                padding: 30px 20px;
            }
            
            .form-title {
                font-size: 24px;
                margin-bottom: 25px;
            }
            
            .login-left h2 {
                font-size: 24px;
            }
            
            .login-left p {
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .login-page-body {
                padding: 5px;
            }
            
            .login-container {
                max-width: 350px;
                border-radius: 12px;
            }
            
            .login-left, .login-right {
                padding: 25px 15px;
            }
            
            .form-title {
                font-size: 20px;
                margin-bottom: 20px;
            }
            
            .login-left h2 {
                font-size: 20px;
            }
            
            .login-left p {
                font-size: 13px;
            }
        }
        
        @media (max-width: 360px) {
            .login-container {
                max-width: 320px;
            }
            
            .login-left, .login-right {
                padding: 20px 12px;
            }
            
            .form-title {
                font-size: 18px;
            }
            
            .login-left h2 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body class="login-page-body">
    <div class="login-container">
        <div class="login-left">
            <div>
                <div class="login-logo">
                    <i class="fa fa-tasks"></i>
                    Task<span class="logo-text-opacity">Master</span>
                </div>
                <div class="welcome-text">
                    Welcome to your professional task management system. 
                    Organize, assign, and track tasks efficiently with our modern dashboard.
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <h2 class="login-title">Welcome Back!</h2>
            <p class="login-subtitle">Please sign in to your account</p>
            
            <?php if (isset($_GET['error'])) { ?>
                <div class="login-alert login-alert-danger" role="alert">
                     <?php echo stripcslashes($_GET['error']); ?>
                </div>
            <?php } ?>
            
            <form method="POST" action="app/login.php">
                <?= csrf_input() ?>
                <div class="login-form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required placeholder="Enter your username">
                    <i class="fa fa-user"></i>
                </div>
                
                <div class="login-form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    <i class="fa fa-lock"></i>
                </div>
                
                <div class="captcha-container">
                    <div class="captcha-question">
                        <i class="fa fa-shield captcha-shield-icon"></i>
                        Security: <?php echo $captcha_question; ?>
                    </div>
                    <input type="number" name="captcha_answer" class="captcha-input" required placeholder="?" min="0" max="20">
                    <a href="login.php?refresh_captcha=1" class="captcha-refresh" title="Refresh Captcha">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fa fa-sign-in"></i> Sign In
                </button>
            </form>
            
            <div class="forgot-password">
                <a href="forgot-password.php">Forgot your password?</a>
            </div>
        </div>
    </div>
</body>
</html>