<?php 
// Start session and check if user is logged in
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    // Include database connection and user functions
    include "DB_connection.php";
    include "app/Model/User.php";
    
    // Get current user ID and information
    $user_id = $_SESSION['id'];
    $user = get_user_by_id($conn, $user_id);
    
    // If user not found, logout
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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Task Master</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
</head>
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .profile-avatar-section {
            position: relative;
            z-index: 2;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            position: relative;
            border: 5px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.6);
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }
        
        .profile-avatar:hover .avatar-upload-overlay {
            opacity: 1;
        }
        
        .profile-name {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .profile-role {
            font-size: 18px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title i {
            color: #667eea;
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            color: #2c3e50;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .avatar-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .avatar-option {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .avatar-option:hover {
            transform: scale(1.1);
            border-color: #667eea;
        }
        
        .avatar-option.selected {
            border-color: #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.4);
        }
        
        .file-upload-area {
            border: 3px dashed #e1e5e9;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8f9fa;
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .file-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        
        .upload-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .upload-text {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .upload-hint {
            color: #adb5bd;
            font-size: 14px;
        }
        
        .preview-container {
            margin-top: 20px;
            text-align: center;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            border: 3px solid #e1e5e9;
        }
        
        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .profile-header {
                padding: 30px 20px;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
            
            .profile-name {
                font-size: 24px;
            }
            
            .avatar-options {
                grid-template-columns: repeat(4, 1fr);
            }
            
            .avatar-option {
                width: 60px;
                height: 60px;
                font-size: 24px;
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
            <div class="profile-container">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar-section">
                        <div class="profile-avatar" onclick="document.getElementById('profile-image-input').click()">
                            <?php if (isset($user['profile_image']) && !empty($user['profile_image'])): ?>
                                <img src="uploads/profiles/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image">
                            <?php else: ?>
                                <div class="avatar-letter" style="background-color: <?= htmlspecialchars($user['avatar_color'] ?? '#f39c12') ?>; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; color: white;">
                                    <?= htmlspecialchars($user['avatar_letter'] ?? strtoupper(substr($user['full_name'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            <div class="avatar-upload-overlay">
                                <i class="fa fa-camera" style="font-size: 24px; color: white;"></i>
                            </div>
                        </div>
                        <div class="profile-name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="profile-role"><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fa fa-check-circle"></i>
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <div class="profile-content">
                    <!-- Personal Information -->
                    <div class="profile-card">
                        <h3 class="card-title">
                            <i class="fa fa-user"></i>
                            Personal Information
                        </h3>
                        
                        <form action="app/update-profile.php" method="POST">
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

                    <!-- Profile Picture Settings -->
                    <div class="profile-card">
                        <h3 class="card-title">
                            <i class="fa fa-image"></i>
                            Profile Picture
                        </h3>
                        
                        <!-- File Upload -->
                        <form action="app/upload-profile-image.php" method="POST" enctype="multipart/form-data" id="upload-form">
                            <div class="file-upload-area" onclick="document.getElementById('profile-image-input').click()">
                                <div class="upload-icon">
                                    <i class="fa fa-cloud-upload"></i>
                                </div>
                                <div class="upload-text">Click to upload or drag & drop</div>
                                <div class="upload-hint">PNG, JPG or JPEG (Max 2MB)</div>
                            </div>
                            
                            <input type="file" id="profile-image-input" name="profile_image" 
                                   accept="image/*" style="display: none;" onchange="previewImage(this)">
                            
                            <div class="preview-container" id="preview-container" style="display: none;">
                                <img id="preview-image" class="preview-image" src="" alt="Preview">
                                <br><br>
                                <button type="submit" class="btn-primary">
                                    <i class="fa fa-upload"></i>
                                    Upload Image
                                </button>
                                <button type="button" class="btn-secondary" onclick="cancelPreview()">
                                    <i class="fa fa-times"></i>
                                    Cancel
                                </button>
                            </div>
                        </form>
                        
                        <!-- Avatar Options -->
                        <div style="margin-top: 30px;">
                            <h4 style="color: #2c3e50; margin-bottom: 15px;">Or choose an avatar:</h4>
                            <div class="avatar-options">
                                <?php 
                                $letters = range('A', 'Z');
                                $colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'];
                                for ($i = 0; $i < 8; $i++):
                                    $letter = $letters[$i];
                                    $color = $colors[$i];
                                ?>
                                    <div class="avatar-option" style="background: <?= $color ?>" 
                                         onclick="selectAvatar('<?= $letter ?>', '<?= $color ?>')">
                                        <?= $letter ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <!-- Remove Picture Button -->
                        <?php if (isset($user['profile_image']) && !empty($user['profile_image'])): ?>
                            <div style="margin-top: 20px;">
                                <form action="app/remove-profile-image.php" method="POST" style="display: inline;">
                                    <button type="submit" class="btn-secondary" 
                                            onclick="return confirm('Are you sure you want to remove your profile picture?')">
                                        <i class="fa fa-trash"></i>
                                        Remove Picture
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('preview-container').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function cancelPreview() {
            document.getElementById('profile-image-input').value = '';
            document.getElementById('preview-container').style.display = 'none';
        }
        
        function selectAvatar(letter, color) {
            // Remove previous selections
            document.querySelectorAll('.avatar-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selection to clicked avatar
            event.target.classList.add('selected');
            
            // Send AJAX request to update avatar
            fetch('app/update-avatar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `avatar_letter=${letter}&avatar_color=${encodeURIComponent(color)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the main profile avatar
                    const profileAvatar = document.querySelector('.profile-avatar');
                    profileAvatar.innerHTML = `${letter}<div class="avatar-upload-overlay"><i class="fa fa-camera" style="font-size: 24px; color: white;"></i></div>`;
                    profileAvatar.style.background = color;
                    
                    // Show success message
                    location.href = 'profile.php?success=Avatar updated successfully';
                } else {
                    alert('Failed to update avatar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the avatar');
            });
        }
        
        // Drag and drop functionality
        const uploadArea = document.querySelector('.file-upload-area');
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('profile-image-input').files = files;
                previewImage(document.getElementById('profile-image-input'));
            }
        });
    </script>
</body>
</html>

<?php
} else {
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>