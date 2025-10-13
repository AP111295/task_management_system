<!DOCTYPE html>
<html>
<head>
    <title>MailHog Status - Task Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .btn { background: #f39c12; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #e67e22; }
        h1 { color: #333; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 MailHog Status Check</h1>
        
        <?php
        // Check if MailHog is running
        $mailhog_running = false;
        $connection = @fsockopen('localhost', 1025, $errno, $errstr, 5);
        if ($connection) {
            $mailhog_running = true;
            fclose($connection);
        }
        
        // Check MailHog web interface
        $web_interface = false;
        $web_connection = @fsockopen('localhost', 8025, $errno, $errstr, 5);
        if ($web_connection) {
            $web_interface = true;
            fclose($web_connection);
        }
        ?>
        
        <div class="status <?= $mailhog_running ? 'success' : 'error' ?>">
            <strong>SMTP Server (Port 1025):</strong> 
            <?= $mailhog_running ? '✅ Running' : '❌ Not Running' ?>
        </div>
        
        <div class="status <?= $web_interface ? 'success' : 'error' ?>">
            <strong>Web Interface (Port 8025):</strong> 
            <?= $web_interface ? '✅ Available' : '❌ Not Available' ?>
        </div>
        
        <div class="status info">
            <strong>Email Configuration:</strong> ✅ Updated for MailHog
            <br><small>SMTP: localhost:1025 | Web UI: localhost:8025</small>
        </div>
        
        <?php if ($mailhog_running && $web_interface): ?>
            <div class="status success">
                <strong>🎉 MailHog is ready!</strong> Your task management system can now send and receive emails for testing.
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="http://localhost:8025" target="_blank" class="btn">📧 Open MailHog Web Interface</a>
                <a href="index.php" class="btn">🏠 Back to Task Manager</a>
            </div>
            
        <?php else: ?>
            <div class="status error">
                <strong>⚠️ MailHog is not running!</strong> 
                <br>Please start MailHog by running the start_mailhog.bat file.
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="javascript:location.reload()" class="btn">🔄 Refresh Status</a>
            </div>
        <?php endif; ?>
        
        <hr style="margin: 30px 0;">
        
        <h3>📋 Quick Setup Instructions:</h3>
        <ol>
            <li><strong>Start MailHog:</strong> Double-click <code>start_mailhog.bat</code> in your project folder</li>
            <li><strong>Web Interface:</strong> Visit <a href="http://localhost:8025" target="_blank">http://localhost:8025</a></li>
            <li><strong>Test Email:</strong> Try password reset or email verification features</li>
            <li><strong>View Emails:</strong> All emails will appear in the MailHog web interface</li>
        </ol>
        
        <div class="status info">
            <strong>💡 Tip:</strong> Keep MailHog running in the background while testing your task management system. All emails (password resets, user invitations, etc.) will be captured and displayed in the web interface.
        </div>
    </div>
</body>
</html>