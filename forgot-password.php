<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* FORGOT PASSWORD RESPONSIVE STYLES */
        .forgot-password-body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        .forgot-password-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 500px;
        }
        
        .forgot-password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .brand-logo {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 30px;
        }
        
        .brand-logo i {
            margin-right: 10px;
        }
        
        .forgot-password-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 32px;
        }
        
        .forgot-password-title {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .forgot-password-subtitle {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        
        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 768px) {
            .forgot-password-body {
                padding: 15px;
            }
            
            .forgot-password-container {
                max-width: 400px;
            }
            
            .forgot-password-card {
                padding: 30px 25px;
                border-radius: 15px;
            }
            
            .brand-logo {
                font-size: 20px;
                margin-bottom: 25px;
            }
            
            .forgot-password-icon {
                width: 70px;
                height: 70px;
                font-size: 28px;
                margin-bottom: 15px;
            }
            
            .forgot-password-title {
                font-size: 24px;
                margin-bottom: 12px;
            }
            
            .forgot-password-subtitle {
                font-size: 14px;
                margin-bottom: 25px;
            }
        }
        
        @media (max-width: 480px) {
            .forgot-password-body {
                padding: 10px;
            }
            
            .forgot-password-container {
                max-width: 350px;
            }
            
            .forgot-password-card {
                padding: 25px 20px;
                border-radius: 12px;
            }
            
            .brand-logo {
                font-size: 18px;
                margin-bottom: 20px;
            }
            
            .forgot-password-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            
            .forgot-password-title {
                font-size: 20px;
                margin-bottom: 10px;
            }
            
            .forgot-password-subtitle {
                font-size: 13px;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 360px) {
            .forgot-password-container {
                max-width: 320px;
            }
            
            .forgot-password-card {
                padding: 20px 15px;
            }
            
            .brand-logo {
                font-size: 16px;
            }
            
            .forgot-password-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .forgot-password-title {
                font-size: 18px;
            }
            
            .forgot-password-subtitle {
                font-size: 12px;
            }
        }
    </style>
</head>
<body class="forgot-password-body">
    <div class="forgot-password-container">
        <!-- Background decorations -->
        <div class="bg-decoration decoration-1"></div>
        <div class="bg-decoration decoration-2"></div>
        <div class="bg-decoration decoration-3"></div>
        
        <div class="forgot-password-card">
            <!-- Header Section -->
            <div class="forgot-password-header">
                <div class="brand-logo">
                    <i class="fa fa-tasks"></i>
                    <span>Task Master</span>
                </div>
                
                <div class="forgot-password-icon">
                    <i class="fa fa-unlock-alt"></i>
                </div>
                
                <h1 class="forgot-password-title">Forgot your password?</h1>
                <p class="forgot-password-subtitle">
                    No worries! Enter your details below and we'll send you a new password to get back into your account.
                </p>
            </div>
            
            <!-- Alert Messages -->
            <?php if (isset($_GET['error'])) {?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span><?php echo stripcslashes($_GET['error']); ?></span>
                </div>
            <?php } ?>

            <?php if (isset($_GET['success'])) {?>
                <div class="alert alert-success" role="alert">
                    <i class="fa fa-check-circle"></i>
                    <span><?php echo stripcslashes($_GET['success']); ?></span>
                </div>
            <?php } ?>
            
            <!-- Instructions -->
            <div class="info-box">
                <div class="info-icon">
                    <i class="fa fa-lightbulb-o"></i>
                </div>
                <div class="info-content">
                    <h4>How it works</h4>
                    <p>Enter your username and email address. We'll verify your account and send a temporary password to your email.</p>
                </div>
            </div>
      
            <!-- Reset Form -->
            <form method="POST" action="app/request-password-reset.php" class="reset-form">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-user"></i>
                        <span>Username</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           name="user_name" 
                           placeholder="Enter your username" 
                           required
                           autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-envelope"></i>
                        <span>Email Address</span>
                    </label>
                    <input type="email" 
                           class="form-control" 
                           name="email" 
                           placeholder="Enter your email address" 
                           required
                           autocomplete="email">
                </div>
                
                <button type="submit" class="btn btn-reset">
                    <i class="fa fa-paper-plane"></i>
                    <span>Send Reset Password</span>
                </button>
            </form>
            
            <!-- Back to Login -->
            <div class="back-section">
                <p>Remember your password?</p>
                <a href="login.php" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Back to Login</span>
                </a>
            </div>
        </div>
        
        <!-- Security Note -->
        <div class="security-note">
            <i class="fa fa-shield"></i>
            <span>Your account security is our priority</span>
        </div>
    </div>
</body>
</html>