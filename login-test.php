<?php
session_start();

// Générer le captcha
if (!isset($_SESSION['captcha_num1']) || !isset($_SESSION['captcha_num2'])) {
    $_SESSION['captcha_num1'] = rand(1, 10);
    $_SESSION['captcha_num2'] = rand(1, 10);
    $_SESSION['captcha_operation'] = rand(0, 1);
}

// Renouveler le captcha si demandé
if (isset($_GET['refresh_captcha'])) {
    $_SESSION['captcha_num1'] = rand(1, 10);
    $_SESSION['captcha_num2'] = rand(1, 10);
    $_SESSION['captcha_operation'] = rand(0, 1);
    header('Location: login-test.php');
    exit;
}

$captcha_question = '';
if ($_SESSION['captcha_operation'] == 0) {
    $captcha_question = $_SESSION['captcha_num1'] . ' + ' . $_SESSION['captcha_num2'] . ' = ?';
} else {
    $num1 = max($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
    $num2 = min($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
    $captcha_question = $num1 . ' - ' . $num2 . ' = ?';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Test - Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Inline styles for testing */
        .login-page-body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 500px;
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            text-align: center;
        }
        
        .login-right {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .login-subtitle {
            color: #666;
            margin-bottom: 40px;
            text-align: center;
            font-size: 16px;
        }
        
        .login-form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .login-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .login-form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            background: #f8f9fa;
            box-sizing: border-box;
        }
        
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="login-page-body">
    <div class="login-container">
        <div class="login-left">
            <div>
                <div style="font-size: 64px; margin-bottom: 20px;">
                    <i class="fa fa-tasks"></i>
                </div>
                <h1>Task Master</h1>
                <p>Welcome to your professional task management system.</p>
            </div>
        </div>

        <div class="login-right">
            <h2 class="login-title">Welcome Back!</h2>
            <p class="login-subtitle">Please sign in to your account</p>

            <form method="POST" action="app/login.php">
                <div class="login-form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required placeholder="Enter your username">
                </div>

                <div class="login-form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                </div>

                <div class="login-form-group">
                    <label>Security: <?php echo $captcha_question; ?></label>
                    <input type="number" name="captcha_answer" required placeholder="Enter answer" min="0" max="20">
                </div>

                <button type="submit" class="login-btn">
                    <i class="fa fa-sign-in"></i> Sign In
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <a href="forgot-password.php" style="color: #667eea; text-decoration: none;">Forgot your password?</a>
            </div>
        </div>
    </div>
</body>
</html>