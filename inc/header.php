<header class="header">
    <div class="header-content">
        <div class="header-left-container">
            <label for="checkbox" class="menu-toggle">
                <i class="fa fa-bars"></i>
            </label>
            <div class="logo">
                <i class="fa fa-tasks"></i>
                Task<span class="logo-text-opacity">Master</span>
            </div>
        </div>
        
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">
                    <?php 
                    if (isset($_SESSION['full_name'])) {
                        echo strtoupper(substr($_SESSION['full_name'], 0, 1));
                    } else {
                        echo 'U';
                    }
                    ?>
                </div>
                <div class="user-details">
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
            </div>
        </div>
    </div>
</header>