<?php
session_start();

// Determine reset type
$reset_type = isset($_GET['type']) ? $_GET['type'] : 'client';
$is_admin_reset = ($reset_type === 'admin');

// Check admin access for admin reset
if ($is_admin_reset) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php?error=Access denied");
        exit();
    }
}

// Get notification ID if present
$notification_id = isset($_GET['notification_id']) ? $_GET['notification_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <style>
        /* RESET PASSWORD RESPONSIVE STYLES */
        .reset-password-body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reset-password-container {
            width: 100%;
            max-width: 500px;
        }
        
        .reset-password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
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
        
        .reset-password-icon {
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
        
        .reset-password-title {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .reset-password-subtitle {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        
        /* FORM STYLES */
        .reset-form {
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            color: #2d3748;
            font-weight: 600;
            font-size: 15px;
        }
        
        .form-label i {
            color: #667eea;
            width: 16px;
        }
        
        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8fafc;
            color: #2d3748;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        .form-control::placeholder {
            color: #a0aec0;
        }
        
        .btn-reset {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        
        .back-section {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }
        
        .back-link:hover {
            color: #764ba2;
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(-2px);
            text-decoration: none;
        }
        
        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 768px) {
            .reset-password-body {
                padding: 15px;
            }
            
            .reset-password-container {
                max-width: 400px;
            }
            
            .reset-password-card {
                padding: 30px 25px;
                border-radius: 15px;
            }
            
            .brand-logo {
                font-size: 20px;
                margin-bottom: 25px;
            }
            
            .reset-password-icon {
                width: 70px;
                height: 70px;
                font-size: 28px;
                margin-bottom: 15px;
            }
            
            .reset-password-title {
                font-size: 24px;
                margin-bottom: 12px;
            }
            
            .reset-password-subtitle {
                font-size: 14px;
                margin-bottom: 25px;
            }
        }
        
        @media (max-width: 480px) {
            .reset-password-body {
                padding: 10px;
            }
            
            .reset-password-container {
                max-width: 350px;
            }
            
            .reset-password-card {
                padding: 25px 20px;
                border-radius: 12px;
            }
            
            .brand-logo {
                font-size: 18px;
                margin-bottom: 20px;
            }
            
            .reset-password-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            
            .reset-password-title {
                font-size: 20px;
                margin-bottom: 10px;
            }
            
            .reset-password-subtitle {
                font-size: 13px;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 360px) {
            .reset-password-container {
                max-width: 320px;
            }
            
            .reset-password-card {
                padding: 20px 15px;
            }
            
            .brand-logo {
                font-size: 16px;
            }
            
            .reset-password-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .reset-password-title {
                font-size: 18px;
            }
            
            .reset-password-subtitle {
                font-size: 12px;
            }
        }
    </style>
</head>
<body class="reset-password-body">
    <div class="reset-password-container">
        <div class="reset-password-card">
            <!-- Simple Header -->
            <div class="reset-password-header">
                <div class="brand-logo">
                    <i class="fa fa-tasks"></i>
                    <span>Task Master</span>
                </div>
                
                <div class="reset-password-icon">
                    <i class="fa fa-<?= $is_admin_reset ? 'user-cog' : 'lock' ?>"></i>
                </div>
                <h1 class="reset-password-title">
                    <?= $is_admin_reset ? 'Admin: Reset User Password' : 'Reset Password' ?>
                </h1>
                <p class="reset-password-subtitle">
                    <?= $is_admin_reset ? 'Manually reset a user\'s password as administrator' : 'Enter your temporary password and create a new one' ?>
                </p>
            </div>
            
            <!-- Alert Messages -->
            <?php if (isset($_GET['success'])) {?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i>
                    <?php echo stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>
            
            <?php if (isset($_GET['error'])) {?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php echo stripcslashes($_GET['error']); ?>
                </div>
            <?php } ?>
      
            <!-- Simple Form -->
            <form method="POST" action="app/process-password-reset.php" class="reset-form">
                <input type="hidden" name="reset_type" value="<?= $reset_type ?>">
                <?php if ($notification_id): ?>
                <input type="hidden" name="notification_id" value="<?= $notification_id ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-user"></i>Username
                    </label>
                    <input type="text" 
                           class="form-control" 
                           name="user_name" 
                           value="<?=isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''?>" 
                           placeholder="<?= $is_admin_reset ? 'Enter username to reset' : 'Enter your username' ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-key"></i><?= $is_admin_reset ? 'Current Password (Optional)' : 'Temporary Password' ?>
                    </label>
                    <input type="<?= $is_admin_reset ? 'password' : 'text' ?>" 
                           class="form-control" 
                           name="temp_password" 
                           placeholder="<?= $is_admin_reset ? 'Leave empty to force reset' : 'Enter temporary password from email' ?>" 
                           <?= $is_admin_reset ? '' : 'required' ?>>
                    <?php if ($is_admin_reset): ?>
                    <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                        <i class="fa fa-info-circle"></i> Leave empty to bypass current password verification
                    </small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-lock"></i>New Password
                    </label>
                    <input type="password" 
                           class="form-control" 
                           name="new_password" 
                           placeholder="Enter new password" 
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-lock"></i>Confirm Password
                    </label>
                    <input type="password" 
                           class="form-control" 
                           name="confirm_password" 
                           placeholder="Confirm new password" 
                           required>
                </div>
                
                <button type="submit" class="btn-reset">
                    <i class="fa fa-save"></i> 
                    <?= $is_admin_reset ? 'Reset User Password' : 'Update Password' ?>
                </button>
            </form>
            
            <!-- Simple Back Link -->
            <div class="back-section">
                <a href="<?= $is_admin_reset ? 'user.php' : 'login.php' ?>" class="back-link">
                    <i class="fa fa-arrow-left"></i> 
                    <?= $is_admin_reset ? 'Back to Users' : 'Back to Login' ?>
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Form validation and user experience improvements
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.reset-form');
            const inputs = form.querySelectorAll('.form-control');
            const submitBtn = form.querySelector('.btn-reset');
            const isAdminReset = <?= $is_admin_reset ? 'true' : 'false' ?>;
            
            // Add focus enhancement
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = '#667eea';
                    this.style.boxShadow = '0 0 0 4px rgba(102, 126, 234, 0.1)';
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.style.borderColor = '#e2e8f0';
                        this.style.boxShadow = 'none';
                    }
                });
            });
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                const username = form.querySelector('input[name="user_name"]').value;
                const tempPassword = form.querySelector('input[name="temp_password"]').value;
                const newPassword = form.querySelector('input[name="new_password"]').value;
                const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
                
                // Different validation for admin vs client
                if (isAdminReset) {
                    if (!username || !newPassword || !confirmPassword) {
                        e.preventDefault();
                        alert('Please fill username and both password fields');
                        return;
                    }
                } else {
                    if (!username || !tempPassword || !newPassword || !confirmPassword) {
                        e.preventDefault();
                        alert('Please fill in all fields');
                        return;
                    }
                }
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New passwords do not match');
                    return;
                }
                
                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long');
                    return;
                }
                
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + (isAdminReset ? 'Resetting...' : 'Updating...');
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>