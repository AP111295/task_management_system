<?php
// Get all employee users with their profile information
function get_all_users($conn) {
    $sql = "SELECT id, full_name, email, username, role, profile_image, avatar_letter, avatar_color FROM users WHERE role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["employee"]);

    if($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();
    } else {
        $users = 0;
    }
    return $users;
}

// Add a new user to the database
function insert_user($conn, $data) {
    $sql = "INSERT INTO users (full_name, email, username, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

// Update existing user information
function update_user($conn, $data){
    $sql = "UPDATE users SET full_name=?, email=?, username=?, password=?, role=? WHERE id=? AND role=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

// Delete a user and all their assigned tasks
function delete_user($conn, $data){
	$user_id = $data[0];
	
	// First delete all tasks assigned to this user
	$sql_tasks = "DELETE FROM tasks WHERE assigned_to = ?";
	$stmt_tasks = $conn->prepare($sql_tasks);
	$stmt_tasks->execute([$user_id]);
	
	// Then delete the user
	$sql = "DELETE FROM users WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$user_id]);
	
	// Return number of deleted rows
	return $stmt->rowCount();
}

// Get a single user by their ID
function get_user_by_id($conn, $id){
    $sql = "SELECT id, full_name, email, username, password, role, profile_image, avatar_letter, avatar_color FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    if ($stmt->rowCount() == 1) {
        return $stmt->fetch();
    } else {
        return 0;
    }
}

// Update user profile image in database
function update_profile_image($conn, $user_id, $image_path) {
    $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$image_path, $user_id]);
}

// Update user avatar letter and color
function update_avatar($conn, $user_id, $letter, $color) {
    $sql = "UPDATE users SET avatar_letter = ?, avatar_color = ?, profile_image = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$letter, $color, $user_id]);
}

// Update user basic information
function update_profile_info($conn, $user_id, $full_name, $email, $username) {
    $sql = "UPDATE users SET full_name = ?, email = ?, username = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$full_name, $email, $username, $user_id]);
}

// Remove profile image and set default avatar
function remove_profile_image($conn, $user_id) {
    $sql = "UPDATE users SET profile_image = NULL, avatar_letter = ?, avatar_color = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute(['U', '#f39c12', $user_id]);
}