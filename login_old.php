<!DOCTYPE html>
<html>
<head>
    <title>Login - Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <?php
    session_start();
    
    // Générer le captcha
    if (!isset($_SESSION['captcha_num1']) || !isset($_SESSION['captcha_num2'])) {
        $_SESSION['captcha_num1'] = rand(1, 10);
        $_SESSION['captcha_num2'] = rand(1, 10);
        $_SESSION['captcha_operation'] = rand(0, 1); // 0 = addition, 1 = soustraction
    }
    
    // Renouveler le captcha si demandé
    if (isset($_GET['refresh_captcha'])) {
        $_SESSION['captcha_num1'] = rand(1, 10);
        $_SESSION['captcha_num2'] = rand(1, 10);
        $_SESSION['captcha_operation'] = rand(0, 1);
        header('Location: login.php');
        exit();
    }
    
    $captcha_question = '';
    $captcha_answer = 0;
    
    if ($_SESSION['captcha_operation'] == 0) {
        $captcha_question = $_SESSION['captcha_num1'] . ' + ' . $_SESSION['captcha_num2'] . ' = ?';
        $captcha_answer = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];
    } else {
        // S'assurer que le résultat est positif
        $num1 = max($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
        $num2 = min($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
        $captcha_question = $num1 . ' - ' . $num2 . ' = ?';
        $captcha_answer = $num1 - $num2;
    }
    
    $_SESSION['captcha_answer'] = $captcha_answer;
    ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
        
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .logo i {
            margin-right: 10px;
        }
        
        .welcome-text {
            font-size: 18px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .login-title {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #7f8c8d;
            margin-bottom: 40px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group i {
            position: absolute;
            right: 15px;
            top: 50px;
            transform: translateY(-50%);
            color: #7f8c8d;
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
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }
        
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: #764ba2;
        }
        
        .captcha-container {
            background: #f8f9fa;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        
        .captcha-question {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .captcha-input {
            width: 80px;
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            text-align: center;
            font-weight: bold;
        }
        
        .captcha-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        .captcha-refresh {
            background: #667eea;
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .captcha-refresh:hover {
            background: #5a6fd8;
            transform: rotate(180deg);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .login-left {
                padding: 30px;
            }
            
            .login-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div>
                <div class="logo">
                    <i class="fa fa-tasks"></i>
                    Task<span style="opacity: 0.8;">Master</span>
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
                <div class="alert alert-danger" role="alert">
                    ❌ <?php echo stripcslashes($_GET['error']); ?>
                </div>
            <?php } ?>
            
            <form method="POST" action="app/login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required placeholder="Enter your username">
                    <i class="fa fa-user"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    <i class="fa fa-lock"></i>
                </div>
                
                <div class="captcha-container">
                    <div class="captcha-question">
                        <i class="fa fa-shield" style="color: #667eea;"></i>
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