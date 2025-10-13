<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    if ($_SESSION['role'] == 'admin') {
        include "DB_connection.php";
        include "app/Model/User.php";

         $user = get_user_by_id($conn, $_GET['id']); 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* RESPONSIVE FORM STYLES */
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
        }
        
        .form-title i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .back-btn {
            background: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .user-profile {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px solid #e9ecef;
        }
        
        .user-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #667eea;
        }
        
        .user-profile h4 {
            color: #495057;
            margin: 0;
            font-size: 18px;
        }
        
        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 768px) {
            .body {
                padding: 15px 10px;
            }
            
            .title {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
                text-align: center;
            }
            
            .back-btn {
                margin: 0 auto;
                width: fit-content;
            }
            
            .form-container {
                margin: 20px 10px;
                padding: 25px 20px;
            }
            
            .form-title {
                font-size: 20px;
                margin-bottom: 25px;
            }
            
            .user-profile {
                margin-bottom: 25px;
                padding: 15px;
            }
            
            .user-profile img {
                width: 70px;
                height: 70px;
            }
            
            .user-profile h4 {
                font-size: 16px;
            }
        }
        
        @media (max-width: 480px) {
            .body {
                padding: 10px 5px;
            }
            
            .form-container {
                margin: 15px 5px;
                padding: 20px 15px;
                border-radius: 8px;
            }
            
            .form-title {
                font-size: 18px;
                margin-bottom: 20px;
            }
            
            .title {
                font-size: 18px;
            }
            
            .back-btn {
                padding: 8px 12px;
                font-size: 13px;
            }
            
            .user-profile {
                padding: 12px;
                margin-bottom: 20px;
            }
            
            .user-profile img {
                width: 60px;
                height: 60px;
            }
            
            .user-profile h4 {
                font-size: 14px;
            }
        }
        
        @media (max-width: 360px) {
            .form-container {
                margin: 10px 3px;
                padding: 15px 10px;
            }
            
            .form-title {
                font-size: 16px;
            }
            
            .title {
                font-size: 16px;
            }
            
            .user-profile img {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <?php include "inc/nav.php"; ?>
    <div class="body">
        <section class="section-1">
            <div class="title">
                <h1>
                    <i class="fa fa-user-edit"></i>
                    Edit User
                </h1>
                <a href="user.php" class="back-btn">
                    <i class="fa fa-arrow-left"></i>
                    Back to Users
                </a>
            </div>
            
            <?php if (isset($_GET['error'])) {?>
                <div class="alert alert-danger" role="alert" style="background: #f8d7da !important; color: #721c24 !important; border: 1px solid #f5c6cb !important; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-exclamation-triangle" style="color: #dc3545; font-size: 18px;"></i>
                    <?php echo stripcslashes($_GET['error']); ?>
                </div>
            <?php } ?>
            
            <?php if (isset($_GET['success'])) {?>
                <div class="alert alert-success" role="alert" style="background: #d4edda !important; color: #155724 !important; border: 1px solid #c3e6cb !important; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-check-circle" style="color: #28a745; font-size: 18px;"></i>
                    <?php echo stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>
            
            <div class="form-container">
                <div class="user-profile">
                    <img src="img/user.jpg" alt="User Avatar">
                    <h4><?=htmlspecialchars($user['full_name'])?></h4>
                </div>
                
                <div class="form-title">
                    <i class="fa fa-user-edit"></i>
                    Edit User Information
                </div>
                
                <form method="POST" action="app/update-user.php" class="responsive-form">
                    <div class="input-holder">
                        <label>
                            <i class="fa fa-user"></i>Full Name
                        </label>
                        <input type="text" 
                               name="full_name" 
                               value="<?=htmlspecialchars($user['full_name'])?>" 
                               required
                               class="form-control"
                               placeholder="Enter full name">
                    </div>
                    
                    <div class="input-holder">
                        <label>
                            <i class="fa fa-envelope"></i>Email Address
                        </label>
                        <input type="email" 
                               name="email" 
                               value="<?=htmlspecialchars($user['email'])?>" 
                               required
                               class="form-control"
                               placeholder="Enter email address">
                    </div>
                    
                    <div class="input-holder">
                        <label>
                            <i class="fa fa-at"></i>Username
                        </label>
                        <input type="text" 
                               name="user_name" 
                               value="<?=htmlspecialchars($user['username'])?>" 
                               required
                               class="form-control"
                               placeholder="Enter username">
                    </div>
                    
                    <input type="hidden" name="id" value="<?=$user['id']?>">
                    
                    <?php if (isset($_GET['notification_id'])) { ?>
                        <input type="hidden" name="notification_id" value="<?=$_GET['notification_id']?>">
                    <?php } ?>
                    
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update User
                        </button>
                        <a href="user.php" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
</html>

<?php 
    } else {
        $em = "You don't have permission to view this page";
        header("Location: index.php?error=$em");
        exit();
    }
} else {
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>