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
    <title>Edit User - Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <div class="edit-user-header">
                <h4 class="title">
                    <i class="fa fa-user-edit"></i> Edit User
                    <a href="user.php" class="back-link">
                        <i class="fa fa-arrow-left"></i> Back to Users
                    </a>
                </h4>
            </div>
            
            <?php if (isset($_GET['error'])) {?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php echo stripcslashes($_GET['error']); ?>
                </div>
            <?php } ?>
            
            <?php if (isset($_GET['success'])) {?>
                <div class="alert alert-success" role="alert">
                    <i class="fa fa-check-circle"></i>
                    <?php echo stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>
            
            <div class="edit-user-container">
                <div class="form-card">
                    <div class="form-header">
                        <h3><i class="fa fa-user"></i> User Information</h3>
                        <p>Update user details below</p>
                    </div>
                    
                    <form method="POST" action="app/update-user.php" class="edit-user-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">
                                    <i class="fa fa-user"></i> Full Name
                                </label>
                                <input type="text" 
                                       id="full_name"
                                       name="full_name" 
                                       value="<?=htmlspecialchars($user['full_name'])?>" 
                                       required
                                       class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">
                                    <i class="fa fa-envelope"></i> Email Address
                                </label>
                                <input type="email" 
                                       id="email"
                                       name="email" 
                                       value="<?=htmlspecialchars($user['email'])?>" 
                                       required
                                       class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="user_name">
                                    <i class="fa fa-at"></i> Username
                                </label>
                                <input type="text" 
                                       id="user_name"
                                       name="user_name" 
                                       value="<?=htmlspecialchars($user['username'])?>" 
                                       required
                                       class="form-control">
                            </div>
                        </div>
                        
                        <input type="hidden" name="id" value="<?=$user['id']?>">
                        
                        <?php if (isset($_GET['notification_id'])) { ?>
                            <input type="hidden" name="notification_id" value="<?=$_GET['notification_id']?>">
                        <?php } ?>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-update">
                                <i class="fa fa-save"></i> Update User
                            </button>
                            <a href="user.php" class="btn-cancel">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
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