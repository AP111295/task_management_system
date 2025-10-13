<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    if ($_SESSION['role'] == 'admin') {
        include "DB_connection.php";
        
        // Préparer la requête sans debug pour le moment
        try {
            $notifications_query = "SELECT * FROM notifications ORDER BY id DESC";
            $notifications_result = $conn->query($notifications_query);
        } catch (PDOException $e) {
            $db_error = $e->getMessage();
        }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-container {
            padding: 20px;
        }
        
        .title {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .notifications-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .notifications-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .notifications-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }
        
        .notifications-table tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.001);
            transition: all 0.2s ease;
        }
        
        .notification-user {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .notification-email {
            color: #7f8c8d;
            font-size: 13px;
        }
        
        .notification-reason {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            color: #f57c00;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #fff3e0;
            display: inline-block;
        }
        
        .notification-date {
            color: #6c757d;
            font-size: 12px;
        }
        
        .process-btn {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        
        .process-btn:hover {
            background: linear-gradient(135deg, #45a049, #4caf50);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #e9ecef;
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
        
        /* RESPONSIVE MOBILE CARD VIEW */
        .mobile-cards {
            display: none;
        }
        
        .notification-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .card-user {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .card-date {
            color: #6c757d;
            font-size: 12px;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 12px;
        }
        
        .card-body {
            margin-bottom: 15px;
        }
        
        .card-email {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-reason {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            color: #f57c00;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .card-actions {
            text-align: right;
        }
        
        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 768px) {
            .body {
                padding: 15px 10px;
            }
            
            .dashboard-container {
                padding: 10px;
            }
            
            .title {
                font-size: 22px;
                flex-direction: column;
                align-items: stretch;
                text-align: center;
                gap: 10px;
            }
            
            .notifications-table {
                display: none;
            }
            
            .mobile-cards {
                display: block;
            }
            
            .alert {
                padding: 12px 15px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .body {
                padding: 10px 5px;
            }
            
            .dashboard-container {
                padding: 5px;
            }
            
            .title {
                font-size: 20px;
                margin-bottom: 20px;
            }
            
            .notification-card {
                padding: 15px;
                margin-bottom: 12px;
                border-radius: 8px;
            }
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            
            .card-user {
                font-size: 15px;
            }
            
            .card-date {
                align-self: flex-start;
                font-size: 11px;
            }
            
            .card-email {
                font-size: 13px;
            }
            
            .card-reason {
                font-size: 12px;
                padding: 6px 10px;
            }
            
            .process-btn {
                padding: 8px 12px;
                font-size: 11px;
                width: 100%;
                text-align: center;
            }
        }
        
        @media (max-width: 360px) {
            .title {
                font-size: 18px;
            }
            
            .notification-card {
                padding: 12px;
            }
            
            .card-user {
                font-size: 14px;
            }
            
            .card-email {
                font-size: 12px;
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
                <h4 class="title">🔔 Notifications</h4>
                
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
                
                <?php if ($notifications_result->rowCount() > 0) { ?>
                <!-- Desktop Table View -->
                <table class="notifications-table">
                    <thead>
                        <tr>
                            <th>👤 User</th>
                            <th>📧 Email</th>
                            <th>🔖 Reason</th>
                            <th>📅 Date</th>
                            <th>⚡ Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $notifications_result->execute(); // Reset the result pointer
                        while($notification = $notifications_result->fetch()) { ?>
                        <tr>
                            <td>
                                <div class="notification-user"><?= htmlspecialchars($notification['username'] ?? 'Unknown User') ?></div>
                                <div class="notification-email">ID: <?= htmlspecialchars($notification['user_id'] ?? 'N/A') ?></div>
                            </td>
                            <td>
                                <div class="notification-email"><?= htmlspecialchars($notification['email'] ?? 'N/A') ?></div>
                            </td>
                            <td>
                                <span class="notification-reason"><?= htmlspecialchars($notification['reason'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <div class="notification-date">
                                    <?php 
                                    if (isset($notification['created_at'])) {
                                        echo date('M d, Y \a\t H:i', strtotime($notification['created_at']));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($notification['status'] == 'pending') { ?>
                                <a href="reset-password.php?type=admin&notification_id=<?= $notification['id'] ?>&username=<?= urlencode($notification['username'] ?? '') ?>" 
                                   class="process-btn">
                                    🔧 Process
                                </a>
                                <?php } else { ?>
                                <span class="notification-processed">✅ Processed</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <!-- Mobile Card View -->
                <div class="mobile-cards">
                    <?php 
                    $notifications_result->execute(); // Reset the result pointer again for mobile cards
                    while($notification = $notifications_result->fetch()) { ?>
                    <div class="notification-card">
                        <div class="card-header">
                            <div class="card-user">
                                <i class="fa fa-user"></i>
                                <?= htmlspecialchars($notification['username'] ?? 'Unknown User') ?>
                            </div>
                            <div class="card-date">
                                <i class="fa fa-clock-o"></i>
                                <?php 
                                if (isset($notification['created_at'])) {
                                    echo date('M d, Y', strtotime($notification['created_at']));
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="card-email">
                                <i class="fa fa-envelope"></i>
                                <?= htmlspecialchars($notification['email'] ?? 'N/A') ?>
                            </div>
                            
                            <div class="card-reason">
                                <i class="fa fa-tag"></i>
                                <?= htmlspecialchars($notification['reason'] ?? 'N/A') ?>
                            </div>
                        </div>
                        
                        <div class="card-actions">
                            <?php if ($notification['status'] == 'pending') { ?>
                            <a href="reset-password.php?type=admin&notification_id=<?= $notification['id'] ?>&username=<?= urlencode($notification['username'] ?? '') ?>" 
                               class="process-btn">
                                <i class="fa fa-cog"></i> Process Request
                            </a>
                            <?php } else { ?>
                            <span class="notification-processed" style="color: #28a745; font-weight: 600;">
                                <i class="fa fa-check-circle"></i> Processed
                            </span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="empty-state">
                    <i class="fa fa-bell-slash"></i>
                    <h3>No Notifications</h3>
                    <p>All caught up! No pending notifications at the moment.</p>
                </div>
                <?php } ?>
            </div>
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