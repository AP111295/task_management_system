<?php
/**
 * Brute Force Protection Utility
 * Tracks login attempts and implements account lockout
 */

// Track failed login attempts
function track_failed_login($username, $ip_address) {
    global $conn;
    
    $sql = "INSERT INTO login_attempts (username, ip_address, attempt_time) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $ip_address]);
}

// Check if account is locked
function is_account_locked($username, $ip_address) {
    global $conn;
    
    // Check attempts in last 15 minutes
    $sql = "SELECT COUNT(*) FROM login_attempts 
            WHERE (username = ? OR ip_address = ?) 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $ip_address]);
    $attempts = $stmt->fetchColumn();
    
    // Lock after 5 failed attempts
    return $attempts >= 5;
}

// Clean old login attempts (older than 1 hour)
function clean_old_attempts() {
    global $conn;
    
    $sql = "DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
}

// Get lockout time remaining
function get_lockout_time_remaining($username, $ip_address) {
    global $conn;
    
    $sql = "SELECT MAX(attempt_time) as last_attempt FROM login_attempts 
            WHERE (username = ? OR ip_address = ?) 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $ip_address]);
    $result = $stmt->fetch();
    
    if ($result && $result['last_attempt']) {
        $last_attempt = strtotime($result['last_attempt']);
        $unlock_time = $last_attempt + (15 * 60); // 15 minutes
        $remaining = $unlock_time - time();
        return max(0, $remaining);
    }
    
    return 0;
}

// Clear successful login attempts
function clear_login_attempts($username, $ip_address) {
    global $conn;
    
    $sql = "DELETE FROM login_attempts WHERE username = ? OR ip_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $ip_address]);
}
?>