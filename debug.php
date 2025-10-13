<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include "DB_connection.php";
include "app/Model/User.php";

echo "<h2>Debug Session Information</h2>";
echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Database User Data:</h3>";
$user = get_user_by_id($conn, $_SESSION['id']);
echo "<pre>";
print_r($user);
echo "</pre>";

echo "<h3>Profile Image Path Check:</h3>";
if (!empty($user['profile_image'])) {
    $image_path = "uploads/profiles/" . $user['profile_image'];
    echo "Image path: " . $image_path . "<br>";
    echo "File exists: " . (file_exists($image_path) ? "YES" : "NO") . "<br>";
    if (file_exists($image_path)) {
        echo "File size: " . filesize($image_path) . " bytes<br>";
        echo "File permissions: " . substr(sprintf('%o', fileperms($image_path)), -4) . "<br>";
        echo "<img src='$image_path' style='max-width: 200px; max-height: 200px;' alt='Profile Image'><br>";
    }
} else {
    echo "No profile image in database<br>";
}

// Update session with latest database data
$_SESSION['profile_image'] = $user['profile_image'];
$_SESSION['avatar_letter'] = $user['avatar_letter'];
$_SESSION['avatar_color'] = $user['avatar_color'];

echo "<h3>Updated Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>