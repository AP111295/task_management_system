<?php 
session_start();
if (isset($_SESSION["role"]) && isset($_SESSION["id"])) {
    include "DB_connection.php";
    include "app/Model/User.php";
    include "app/csrf_protection.php";
    
    $user = get_user_by_id($conn, $_SESSION["id"]);
    
    if ($user == 0) {
        header("Location: logout.php");
        exit();
    }
    
    // Update session with latest data from database
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['profile_image'] = $user['profile_image'];
    $_SESSION['avatar_letter'] = $user['avatar_letter'];
    $_SESSION['avatar_color'] = $user['avatar_color'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <?php include "inc/nav.php"; ?>

    <div class="body">
        <section class="section-1">
            <div class="profile-container">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar-section">
                        <div class="profile-avatar">
                            <?php if (isset($user['profile_image']) && !empty($user['profile_image'])): ?>
                                <img src="uploads/profiles/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image">
                            <?php else: ?>
                                <div class="avatar-letter" style="background-color: <?= htmlspecialchars($user['avatar_color'] ?? '#f39c12') ?>;">
                                    <?= htmlspecialchars($user['avatar_letter'] ?? strtoupper(substr($user['full_name'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="profile-name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="profile-role"><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <div class="profile-content">
                    <!-- Profile Picture Upload -->
                    <div class="profile-card">
                        <h3 class="card-title">
                            <i class="fa fa-camera"></i>
                            Profile Picture
                        </h3>
                        
                        <form action="app/update-profile.php" method="POST" enctype="multipart/form-data">
                            <?= csrf_input() ?>
                            <div class="form-group">
                                <label class="form-label">Upload Profile Picture</label>
                                <input type="file" class="form-control" name="profile_image" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif">
                                <small class="form-text">Supported formats: JPG, JPEG, PNG, GIF. Max size: 5MB</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Or choose an avatar color</label>
                                <div class="color-picker">
                                    <input type="color" class="form-control color-input" name="avatar_color" 
                                           value="<?= htmlspecialchars($user['avatar_color'] ?? '#f39c12') ?>">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-primary">
                                <i class="fa fa-upload"></i>
                                Update Picture
                            </button>
                            
                            <?php if (isset($user['profile_image']) && !empty($user['profile_image'])): ?>
                            <button type="submit" name="remove_image" value="1" class="btn-secondary" 
                                    onclick="return confirm('Are you sure you want to remove your profile picture?')">
                                <i class="fa fa-trash"></i>
                                Remove Picture
                            </button>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- Personal Information -->
                    <div class="profile-card">
                        <h3 class="card-title">
                            <i class="fa fa-user"></i>
                            Personal Information
                        </h3>
                        
                        <form action="app/update-profile.php" method="POST">
                            <?= csrf_input() ?>
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" 
                                       value="<?= htmlspecialchars($user['full_name']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" 
                                       value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                            
                            <button type="submit" class="btn-primary">
                                <i class="fa fa-save"></i>
                                Update Information
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Section -->
                    <div class="profile-card">
                        <h3 class="card-title">
                            <i class="fa fa-lock"></i>
                            Change Password
                        </h3>
                        
                        <form action="app/change-password.php" method="POST">
                            <?= csrf_input() ?>
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" 
                                       placeholder="Enter your current password" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" 
                                       placeholder="Enter your new password" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" 
                                       placeholder="Confirm your new password" required>
                            </div>
                            
                            <button type="submit" class="btn-primary">
                                <i class="fa fa-key"></i>
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>

<?php
} else {
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>
