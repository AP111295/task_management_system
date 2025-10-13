<nav class="side-bar">
    <div class="user-p">
        <div class="user-avatar">
            <?php 
            // Check if user has profile image
            if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])) {
                echo '<img src="uploads/profiles/' . htmlspecialchars($_SESSION['profile_image']) . '" alt="Profile" class="profile-img">';
            } else {
                // Use avatar letter and color
                $avatar_letter = isset($_SESSION['avatar_letter']) ? $_SESSION['avatar_letter'] : 'U';
                $avatar_color = isset($_SESSION['avatar_color']) ? $_SESSION['avatar_color'] : '#f39c12';
                echo '<div class="avatar-letter" style="background-color: ' . htmlspecialchars($avatar_color) . '">' . 
                     htmlspecialchars($avatar_letter) . '</div>';
            }
            ?>
        </div>
        <div class="user-name">
            <?php 
            if (isset($_SESSION['full_name'])) {
                echo htmlspecialchars($_SESSION['full_name']);
            } else {
                echo 'User';
            }
            ?>
        </div>
        <div class="user-role">
            <?php 
            if (isset($_SESSION['role'])) {
                echo htmlspecialchars($_SESSION['role']);
            } else {
                echo 'Guest';
            }
            ?>
        </div>
    </div>
    
    <div class="nav-menu">
        <div class="nav-item">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fa fa-tachometer"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
        <div class="nav-item">
            <a href="user.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : ''; ?>">
                <i class="fa fa-users"></i>
                <span>Manage Users</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="notifications.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
                <i class="fa fa-bell"></i>
                <span>Notifications</span>
            </a>
        </div>
        <?php } ?>
        
        <div class="nav-item">
            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fa fa-user"></i>
                <span>Profile</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fa fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="nav-item nav-logout-item">
            <a href="app/logout.php" class="nav-link nav-logout-link">
                <i class="fa fa-sign-out"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('checkbox');
    const sidebar = document.querySelector('.side-bar');
    
    if (window.innerWidth <= 768) {
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !e.target.closest('.menu-toggle')) {
                checkbox.checked = false;
            }
        });
    }
});
</script>