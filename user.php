<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    if ($_SESSION['role'] == 'admin') {
        include "DB_connection.php";
        
        // Récupérer tous les utilisateurs
        $users_query = "SELECT u.*, 
                              COUNT(t.id) as total_tasks,
                              SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks
                       FROM users u 
                       LEFT JOIN tasks t ON u.id = t.assigned_to 
                       GROUP BY u.id 
                       ORDER BY u.role, u.full_name";
        $users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* BASE STYLES */
        .dashboard-container {
            padding: 20px;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        .title {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
        }
        
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .search-input {
            padding: 12px;
            border: 2px solid #e3f2fd;
            border-radius: 8px;
            width: 100%;
            max-width: 350px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #2196f3;
        }
        
        .add-user-btn {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            white-space: nowrap;
        }
        
        .add-user-btn:hover {
            background: linear-gradient(135deg, #45a049, #4caf50);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        /* TABLE CONTAINER */
        .table-container {
            width: 100%;
            overflow: hidden;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .desktop-table-view {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .users-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .users-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }
        
        .users-table tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.001);
            transition: all 0.2s ease;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .user-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .user-username {
            color: #7f8c8d;
            font-size: 13px;
            font-style: italic;
        }
        
        .user-email {
            color: #34495e;
            font-size: 13px;
        }
        
        .role-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .role-admin {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .role-employee {
            background: linear-gradient(135deg, #4ecdc4, #44a08d);
            color: white;
        }
        
        .task-stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .stat-total {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1976d2;
        }
        
        .stat-completed {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            color: #2e7d32;
        }
        
        .user-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        .btn-reset:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(243, 156, 18, 0.3);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .search-icon {
            color: #667eea;
            font-size: 16px;
        }

        /* MOBILE CARD VIEW STYLES */
        .mobile-view {
            display: none;
        }

        .user-mobile-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
            transition: transform 0.2s ease;
        }

        .user-mobile-card:hover {
            transform: translateY(-2px);
        }

        .mobile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .mobile-user-info h3 {
            margin: 0 0 4px 0;
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
        }

        .mobile-user-info .username {
            color: #667eea;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .mobile-user-info .email {
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .mobile-user-info .role {
            background: #f8f9fa;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }

        .mobile-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        
        .edit-btn, .reset-btn, .delete-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .edit-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .edit-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .reset-btn {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        .reset-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }
        
        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .delete-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        .mobile-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .mobile-stat-item {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 8px;
            text-align: center;
            font-size: 11px;
        }

        .mobile-stat-number {
            display: block;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .mobile-stat-item.total {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1976d2;
        }

        .mobile-stat-item.completed {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            color: #2e7d32;
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 768px) {
            .desktop-table-view {
                display: none;
            }
            
            .mobile-view {
                display: block;
            }
            
            .dashboard-container {
                padding: 15px 10px;
            }
            
            .title {
                font-size: 24px;
                margin-bottom: 20px;
            }
            
            .search-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            
            .search-input {
                max-width: 100%;
            }
            
            .add-user-btn {
                text-align: center;
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .dashboard-container {
                padding: 12px 8px;
            }
            
            .title {
                font-size: 22px;
            }
            
            .user-mobile-card {
                padding: 14px;
                margin-bottom: 12px;
            }
            
            .mobile-user-info h3 {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                padding: 8px 5px;
            }
            
            .title {
                font-size: 20px;
            }
            
            .user-mobile-card {
                padding: 12px;
                margin-bottom: 10px;
            }
            
            .mobile-card-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .mobile-actions {
                justify-content: center;
            }
            
            .mobile-user-info h3 {
                font-size: 15px;
            }
        }

        @media (max-width: 360px) {
            .dashboard-container {
                padding: 5px 3px;
            }
            
            .user-mobile-card {
                padding: 10px;
                margin-bottom: 8px;
            }
            
            .mobile-user-info h3 {
                font-size: 14px;
            }
        }

        @media (min-width: 769px) {
            .mobile-view {
                display: none;
            }
            
            .desktop-table-view {
                display: block;
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
            <div class="dashboard-container">
                <h4 class="title">👥 Manage Users</h4>
                
                <?php if (isset($_GET['success'])) {?>
                    <div class="alert alert-success" role="alert">
                        ✅ <?php echo stripcslashes($_GET['success']); ?>
                    </div>
                <?php } ?>
                
                <?php if (isset($_GET['error'])) {?>
                    <div class="alert alert-danger" role="alert">
                        ❌ <?php echo stripcslashes($_GET['error']); ?>
                    </div>
                <?php } ?>
                
                <div class="search-bar">
                    <div class="user-actions-flex">
                        <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search users by name, username or email..." onkeyup="filterUsers()">
                        <i class="fa fa-search search-icon"></i>
                    </div>
                    <a href="add-user.php" class="add-user-btn">
                        <i class="fa fa-plus"></i> Add New User
                    </a>
                </div>
                
                <!-- Desktop Table View -->
                <div class="table-container">
                    <div class="desktop-table-view">
                        <table class="users-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>👤 User Information</th>
                            <th>📧 Contact Details</th>
                            <th>🔖 Role</th>
                            <th>📊 Task Stats</th>
                            <th>⚡ Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users_result->fetch()) { ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <span class="user-name"><?= htmlspecialchars($user['full_name']) ?></span>
                                    <span class="user-username">@<?= htmlspecialchars($user['username']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="user-info">
                                    <span class="user-email"><?= htmlspecialchars($user['email']) ?></span>
                                    <span class="text-muted-small">
                                        📅 Joined: 
                                        <?php 
                                        // Debug: afficher les clés disponibles
                                        if (isset($user['created_at'])) {
                                            echo date('M Y', strtotime($user['created_at']));
                                        } else {
                                            // Chercher d'autres colonnes de date possibles
                                            $date_found = false;
                                            foreach (['created_at', 'date_created', 'registration_date', 'join_date'] as $date_col) {
                                                if (isset($user[$date_col])) {
                                                    echo date('M Y', strtotime($user[$date_col]));
                                                    $date_found = true;
                                                    break;
                                                }
                                            }
                                            if (!$date_found) {
                                                echo "Unknown";
                                                // Debug: afficher les clés disponibles (supprimer après debug)
                                                echo " <!-- Colonnes: " . implode(', ', array_keys($user)) . " -->";
                                            }
                                        }
                                        ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-<?= $user['role'] ?>">
                                    <?= $user['role'] == 'admin' ? '👑 Admin' : '👨‍💼 Employee' ?>
                                </span>
                            </td>
                            <td>
                                <?php if($user['role'] == 'employee') { ?>
                                <div class="task-stats">
                                    <div class="stat-item stat-total">
                                        <i class="fa fa-tasks"></i>
                                        <span>Total: <?= $user['total_tasks'] ?></span>
                                    </div>
                                    <div class="stat-item stat-completed">
                                        <i class="fa fa-check-circle"></i>
                                        <span>Completed: <?= $user['completed_tasks'] ?></span>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <span class="admin-badge">Administrator</span>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="user-actions">
                                    <a href="edit-user.php?id=<?= $user['id'] ?>" class="action-btn btn-edit">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <?php if($user['id'] != $_SESSION['id']) { ?>
                                    <a href="delete-user.php?id=<?= $user['id'] ?>" 
                                       class="action-btn btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                    </div>
                </div>

                <!-- Mobile View -->
                <div class="mobile-view">
                    <?php 
                    // Reset the result pointer for mobile view
                    $users_result = $conn->query($users_query);
                    while ($user = $users_result->fetch()) { 
                    ?>
                        <div class="user-mobile-card">
                            <div class="mobile-card-header">
                                <div class="mobile-user-info">
                                    <h3><?= htmlspecialchars($user['full_name']) ?></h3>
                                    <div class="username">@<?= htmlspecialchars($user['username']) ?></div>
                                    <div class="email"><?= htmlspecialchars($user['email']) ?></div>
                                    <span class="role"><?= ucfirst($user['role']) ?></span>
                                </div>
                                <div class="mobile-actions">
                                    <a href="edit-user.php?id=<?= $user['id'] ?>" class="edit-btn" title="Edit user">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <?php if($user['role'] !== 'admin') { ?>
                                    <a href="delete-user.php?id=<?= $user['id'] ?>" class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this user?')" title="Delete user">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            <div class="mobile-stats">
                                <div class="mobile-stat-item total">
                                    <span class="mobile-stat-number"><?= $user['total_tasks'] ?></span>
                                    <span>Total Tasks</span>
                                </div>
                                <div class="mobile-stat-item completed">
                                    <span class="mobile-stat-number"><?= $user['completed_tasks'] ?></span>
                                    <span>Completed</span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            
            <script>
                function filterUsers() {
                    const input = document.getElementById('searchInput');
                    const filter = input.value.toLowerCase();
                    
                    // Filter desktop table
                    const table = document.getElementById('usersTable');
                    const rows = table.getElementsByTagName('tr');

                    for (let i = 1; i < rows.length; i++) {
                        const nameCell = rows[i].getElementsByTagName('td')[0];
                        const emailCell = rows[i].getElementsByTagName('td')[1];
                        
                        if (nameCell && emailCell) {
                            const nameText = nameCell.textContent.toLowerCase();
                            const emailText = emailCell.textContent.toLowerCase();
                            
                            if (nameText.includes(filter) || emailText.includes(filter)) {
                                rows[i].style.display = '';
                            } else {
                                rows[i].style.display = 'none';
                            }
                        }
                    }
                    
                    // Filter mobile cards
                    const mobileCards = document.querySelectorAll('.user-mobile-card');
                    mobileCards.forEach(card => {
                        const nameText = card.querySelector('.mobile-user-info h3').textContent.toLowerCase();
                        const usernameText = card.querySelector('.mobile-user-info .username').textContent.toLowerCase();
                        const emailText = card.querySelector('.mobile-user-info .email').textContent.toLowerCase();
                        
                        if (nameText.includes(filter) || usernameText.includes(filter) || emailText.includes(filter)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            </script>
        </section>
    </div>
</body>
</html>

<?php 
    } else {
        $em = "You don't have permission to view this page";
        header("Location: login.php?error=$em");
        exit();
    }
} else {
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>