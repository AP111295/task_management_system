<?php
// Start user session to check login status
session_start();

// Check if user is logged in
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

	// Check if user is admin
	if ($_SESSION['role'] == 'admin') {
		// Include database connection
		include "DB_connection.php";

		// Get all users with their task counts
		$users_query = "SELECT u.id, u.username, u.full_name, u.email,
                              COUNT(t.id) as total_tasks,
                              SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
                              SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_tasks,
                              SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks
                       FROM users u 
                       LEFT JOIN tasks t ON u.id = t.assigned_to 
                       WHERE u.role = 'employee'
                       GROUP BY u.id 
                       ORDER BY u.full_name";
		$users_result = $conn->query($users_query);
?>
		<!DOCTYPE html>
		<html>

		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Tasks Dashboard</title>
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
			<link rel="stylesheet" href="css/style.css">
			<style>
				/* BASE STYLES */
				.dashboard-container {
					padding: 20px;
					max-width: 100%;
					overflow-x: hidden;
				}

				.search-bar {
					margin-bottom: 20px;
					display: flex;
					gap: 10px;
					align-items: center;
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

				/* RESPONSIVE CONTAINER */
				.table-container {
					width: 100%;
					overflow: hidden;
					background: white;
					border-radius: 12px;
					box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
				}

				/* DESKTOP TABLE STYLES */
				.desktop-table-view {
					overflow-x: auto;
					-webkit-overflow-scrolling: touch;
				}

				.dashboard-table {
					width: 100%;
					border-collapse: collapse;
					background: white;
					margin: 0;
					min-width: 800px;
				}

				.dashboard-table th {
					background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
					color: white;
					padding: 18px 15px;
					text-align: left;
					font-weight: 600;
					font-size: 14px;
					text-transform: uppercase;
					letter-spacing: 0.5px;
				}

				.dashboard-table td {
					padding: 20px 15px;
					border-bottom: 1px solid #f0f0f0;
					vertical-align: top;
				}

				.user-row {
					cursor: pointer;
					transition: all 0.2s ease;
				}

				.user-row:hover {
					background-color: #f8f9ff;
					transform: scale(1.001);
				}

				.user-row.expanded {
					background-color: #e3f2fd;
				}

				.tasks-row {
					display: none;
					background-color: #f8f9fa;
				}

				.tasks-row.show {
					display: table-row;
				}

				.tasks-container {
					padding: 20px;
					max-height: 400px;
					overflow-y: auto;
				}

				.task-item {
					background: white;
					border-radius: 8px;
					padding: 15px;
					margin-bottom: 10px;
					border-left: 4px solid #667eea;
					box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
					transition: transform 0.2s ease;
				}

				.task-item:hover {
					transform: translateX(5px);
				}

				.task-title {
					font-weight: bold;
					color: #2c3e50;
					font-size: 16px;
					margin-bottom: 8px;
				}

				.task-description {
					color: #7f8c8d;
					font-size: 14px;
					margin-bottom: 10px;
					line-height: 1.4;
				}

				.task-meta {
					display: flex;
					justify-content: space-between;
					align-items: center;
					font-size: 12px;
				}

				.task-status {
					padding: 4px 12px;
					border-radius: 15px;
					font-weight: bold;
					text-transform: uppercase;
				}

				.status-pending {
					background: #fff3cd;
					color: #856404;
				}

				.status-in_progress {
					background: #d1ecf1;
					color: #0c5460;
				}

				.status-completed {
					background: #d4edda;
					color: #155724;
				}

				.task-date {
					color: #6c757d;
				}

				.no-tasks {
					text-align: center;
					color: #6c757d;
					font-style: italic;
					padding: 30px;
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

				.task-stats {
					display: flex;
					flex-direction: column;
					gap: 8px;
				}

				.stat-item {
					display: flex;
					align-items: center;
					gap: 8px;
					padding: 8px 12px;
					border-radius: 20px;
					font-size: 13px;
					font-weight: 500;
					min-width: 120px;
				}

				.stat-total {
					background: linear-gradient(135deg, #e3f2fd, #bbdefb);
					color: #1976d2;
					border: 1px solid #e3f2fd;
				}

				.stat-completed {
					background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
					color: #2e7d32;
					border: 1px solid #e8f5e8;
				}

				.stat-pending {
					background: linear-gradient(135deg, #fff3e0, #ffe0b2);
					color: #f57c00;
					border: 1px solid #fff3e0;
				}

				.stat-progress {
					background: linear-gradient(135deg, #f3e5f5, #e1bee7);
					color: #7b1fa2;
					border: 1px solid #f3e5f5;
				}

				.actions-cell {
					display: flex;
					gap: 10px;
					align-items: center;
				}

				.add-task-btn {
					background: linear-gradient(135deg, #4caf50, #45a049);
					color: white;
					border: none;
					padding: 12px 15px;
					border-radius: 50%;
					cursor: pointer;
					font-size: 18px;
					transition: all 0.3s ease;
					box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
				}

				.add-task-btn:hover {
					background: linear-gradient(135deg, #45a049, #4caf50);
					transform: scale(1.1) rotate(90deg);
					box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
				}

				.expand-btn {
					background: linear-gradient(135deg, #2196f3, #1976d2);
					color: white;
					border: none;
					width: 38px;
					height: 38px;
					border-radius: 50%;
					cursor: pointer;
					font-size: 14px;
					transition: all 0.3s ease;
					box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
					display: flex;
					align-items: center;
					justify-content: center;
					padding: 0;
				}

				.expand-btn:hover {
					transform: scale(1.1);
					box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
				}

				.expand-btn.expanded {
					transform: rotate(180deg);
				}

				/* Modal Styles */
				.modal {
					display: none;
					position: fixed;
					z-index: 1000;
					left: 0;
					top: 0;
					width: 100%;
					height: 100%;
					background-color: rgba(0, 0, 0, 0.6);
					backdrop-filter: blur(5px);
				}

				.modal-content {
					background-color: white;
					margin: 3% auto;
					padding: 0;
					border-radius: 15px;
					width: 90%;
					max-width: 550px;
					box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
					animation: modalSlideIn 0.3s ease;
				}

				@keyframes modalSlideIn {
					from {
						transform: translateY(-50px);
						opacity: 0;
					}

					to {
						transform: translateY(0);
						opacity: 1;
					}
				}

				.modal-header {
					background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
					color: white;
					padding: 25px;
					border-radius: 15px 15px 0 0;
				}

				.modal-header h3 {
					margin: 0;
					font-size: 20px;
					font-weight: 600;
				}

				.modal-body {
					padding: 25px;
				}

				.modal-footer {
					padding: 20px 25px;
					border-top: 1px solid #eee;
					display: flex;
					justify-content: space-between;
					background: #f8f9fa;
					border-radius: 0 0 15px 15px;
				}

				.form-group {
					margin-bottom: 20px;
				}

				.form-group label {
					display: block;
					margin-bottom: 8px;
					font-weight: 600;
					color: #2c3e50;
					font-size: 14px;
				}

				.form-group input,
				.form-group select,
				.form-group textarea {
					width: 100%;
					padding: 12px 15px;
					border: 2px solid #e9ecef;
					border-radius: 8px;
					font-size: 14px;
					transition: border-color 0.3s;
					box-sizing: border-box;
				}

				.form-group input:focus,
				.form-group select:focus,
				.form-group textarea:focus {
					outline: none;
					border-color: #667eea;
				}

				.form-group textarea {
					height: 100px;
					resize: vertical;
					font-family: inherit;
				}

				.btn {
					padding: 12px 25px;
					border: none;
					border-radius: 8px;
					cursor: pointer;
					font-size: 14px;
					font-weight: 600;
					transition: all 0.3s;
					text-transform: uppercase;
					letter-spacing: 0.5px;
				}

				.btn-primary {
					background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
					color: white;
				}

				.btn-primary:hover {
					transform: translateY(-2px);
					box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
				}

				.btn-secondary {
					background: #6c757d;
					color: white;
				}

				.btn-secondary:hover {
					background: #5a6268;
					transform: translateY(-2px);
				}

				.title {
					color: #2c3e50;
					margin-bottom: 30px;
					font-size: 28px;
					font-weight: 700;
				}

				.search-icon {
					color: #667eea;
					font-size: 16px;
				}

				.task-item {
					background: white;
					border-radius: 8px;
					padding: 15px;
					margin-bottom: 10px;
					border-left: 4px solid #667eea;
					box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
					transition: transform 0.2s ease;
					display: flex;
					justify-content: space-between;
					align-items: flex-start;
				}

				.task-item:hover {
					transform: translateX(5px);
				}

				.task-content {
					flex: 1;
				}

				.task-actions {
					margin-left: 15px;
					display: flex;
					align-items: flex-start;
				}

				.delete-task-btn {
					background: linear-gradient(135deg, #e74c3c, #c0392b);
					color: white;
					border: none;
					padding: 8px 10px;
					border-radius: 50%;
					cursor: pointer;
					font-size: 12px;
					transition: all 0.3s ease;
					box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
				}

				.delete-task-btn:hover {
					background: linear-gradient(135deg, #c0392b, #a93226);
					transform: scale(1.1);
					box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
				}

				.delete-task-btn:active {
					transform: scale(0.95);
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
					position: relative;
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
				}

				.mobile-actions {
					display: flex;
					gap: 8px;
					flex-shrink: 0;
				}

				.mobile-stats-grid {
					display: grid;
					grid-template-columns: repeat(2, 1fr);
					gap: 8px;
					margin-bottom: 12px;
				}

				.mobile-stat-item {
					background: #f8f9fa;
					padding: 12px 8px;
					border-radius: 8px;
					text-align: center;
					transition: transform 0.2s ease;
				}

				.mobile-stat-item:hover {
					transform: translateY(-2px);
				}

				.mobile-stat-number {
					display: block;
					font-size: 20px;
					font-weight: bold;
					margin-bottom: 4px;
				}

				.mobile-stat-label {
					font-size: 11px;
					text-transform: uppercase;
					letter-spacing: 0.5px;
				}

				.mobile-stat-item.total {
					background: linear-gradient(135deg, #e3f2fd, #bbdefb);
					color: #1976d2;
				}

				.mobile-stat-item.completed {
					background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
					color: #2e7d32;
				}

				.mobile-stat-item.pending {
					background: linear-gradient(135deg, #fff3e0, #ffe0b2);
					color: #f57c00;
				}

				.mobile-stat-item.progress {
					background: linear-gradient(135deg, #f3e5f5, #e1bee7);
					color: #7b1fa2;
				}

				.mobile-expand-area {
					border-top: 1px solid #f0f0f0;
					margin-top: 12px;
					padding-top: 12px;
				}

				.mobile-tasks-container {
					display: none;
				}

				.mobile-tasks-container.show {
					display: block;
				}

				/* RESPONSIVE BREAKPOINTS */

				/* RESPONSIVE BREAKPOINTS */

				/* Show mobile view on tablets and smaller */
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
					
					.search-input {
						max-width: 100%;
					}
				}

				/* Enhanced mobile optimizations */
				@media (max-width: 640px) {
					.dashboard-container {
						padding: 12px 8px;
					}
					
					.user-mobile-card {
						padding: 14px;
						margin-bottom: 12px;
					}
					
					.mobile-user-info h3 {
						font-size: 16px;
					}
					
					.mobile-stat-number {
						font-size: 18px;
					}
					
					.mobile-stat-label {
						font-size: 10px;
					}
				}

				/* Small mobile optimizations */
				@media (max-width: 480px) {
					.dashboard-container {
						padding: 8px 5px;
					}
					
					.user-mobile-card {
						padding: 12px;
						margin-bottom: 10px;
						border-radius: 8px;
					}
					
					.mobile-card-header {
						flex-direction: column;
						align-items: stretch;
						gap: 10px;
					}
					
					.mobile-actions {
						justify-content: center;
					}
					
					.mobile-user-info h3 {
						font-size: 15px;
					}
					
					.mobile-user-info .username,
					.mobile-user-info .email {
						font-size: 12px;
					}
					
					.mobile-stat-item {
						padding: 10px 6px;
					}
					
					.mobile-stat-number {
						font-size: 16px;
					}
					
					.mobile-stat-label {
						font-size: 9px;
					}
				}

				/* Extra small devices */
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
					
					.mobile-stat-item {
						padding: 8px 4px;
					}
					
					.mobile-stat-number {
						font-size: 14px;
					}
					
					.mobile-stat-label {
						font-size: 8px;
					}
					
					.expand-btn,
					.add-task-btn {
						width: 32px;
						height: 32px;
						font-size: 12px;
					}
				}

				/* Keep desktop table for larger screens only */
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
						<h4 class="title">📋 Tasks Dashboard</h4>

						<?php if (isset($_GET['success'])) { ?>
							<div class="alert alert-success" role="alert">
								✅ <?php echo stripcslashes($_GET['success']); ?>
							</div>
						<?php } ?>

						<?php if (isset($_GET['error'])) { ?>
							<div class="alert alert-danger" role="alert">
								❌ <?php echo stripcslashes($_GET['error']); ?>
							</div>
						<?php } ?>

						<div class="search-bar">
							<input type="text" id="searchInput" class="search-input" placeholder="🔍 Search users by name, username or email..." onkeyup="filterUsers()">
							<i class="fa fa-search search-icon"></i>
						</div>

						<!-- Desktop Table View -->
						<div class="table-container">
							<div class="desktop-table-view">
								<table class="dashboard-table" id="usersTable">
							<thead>
								<tr>
									<th>👤 User Information</th>
									<th>📧 Contact Details</th>
									<th>📊 Task Statistics</th>
									<th>⚡ Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php while ($user = $users_result->fetch()) { ?>
									<tr class="user-row" onclick="toggleTasks(<?= $user['id'] ?>)">
										<td>
											<div class="user-info">
												<span class="user-name"><?= htmlspecialchars($user['full_name']) ?></span>
												<span class="user-username">@<?= htmlspecialchars($user['username']) ?></span>
											</div>
										</td>
										<td>
											<div class="user-info">
												<span class="user-email"><?= htmlspecialchars($user['email']) ?></span>
												<span class="employee-badge">👨‍💼 Employee</span>
											</div>
										</td>
										<td>
											<div class="task-stats">
												<div class="stat-item stat-total">
													<i class="fa fa-tasks"></i>
													<span>Total: <?= $user['total_tasks'] ?></span>
												</div>
												<div class="stat-item stat-completed">
													<i class="fa fa-check-circle"></i>
													<span>Completed: <?= $user['completed_tasks'] ?></span>
												</div>
												<div class="stat-item stat-pending">
													<i class="fa fa-clock-o"></i>
													<span>Pending: <?= $user['pending_tasks'] ?></span>
												</div>
												<div class="stat-item stat-progress">
													<i class="fa fa-spinner"></i>
													<span>In Progress: <?= $user['in_progress_tasks'] ?></span>
												</div>
											</div>
										</td>
										<td>
											<div class="actions-cell">
												<button class="expand-btn" id="expand-btn-<?= $user['id'] ?>" onclick="event.stopPropagation(); toggleTasks(<?= $user['id'] ?>)" title="View tasks">
													<i class="fa fa-chevron-down"></i>
												</button>
												<button class="add-task-btn" onclick="event.stopPropagation(); openTaskModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['full_name']) ?>')" title="Assign new task">
													<i class="fa fa-plus"></i>
												</button>
											</div>
										</td>
									</tr>

									<!-- Ligne déroulante pour les tâches -->
									<tr class="tasks-row" id="tasks-<?= $user['id'] ?>">
										<td colspan="4">
											<div class="tasks-container" id="tasks-container-<?= $user['id'] ?>">
												<!-- Les tâches seront chargées ici via AJAX -->
												<div class="no-tasks">Loading tasks...</div>
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
								<div class="user-mobile-card" id="mobile-card-<?= $user['id'] ?>">
									<div class="mobile-card-header">
										<div class="mobile-user-info">
											<h3><?= htmlspecialchars($user['full_name']) ?></h3>
											<div class="username">@<?= htmlspecialchars($user['username']) ?></div>
											<div class="email"><?= htmlspecialchars($user['email']) ?></div>
										</div>
										<div class="mobile-actions">
											<button class="expand-btn" onclick="toggleMobileTasks(<?= $user['id'] ?>)" title="View tasks">
												<i class="fa fa-chevron-down"></i>
											</button>
											<button class="add-task-btn" onclick="openTaskModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['full_name']) ?>')" title="Assign new task">
												<i class="fa fa-plus"></i>
											</button>
										</div>
									</div>
									
									<div class="mobile-stats-grid">
										<div class="mobile-stat-item total">
											<span class="mobile-stat-number"><?= $user['total_tasks'] ?></span>
											<span class="mobile-stat-label">Total Tasks</span>
										</div>
										<div class="mobile-stat-item completed">
											<span class="mobile-stat-number"><?= $user['completed_tasks'] ?></span>
											<span class="mobile-stat-label">Completed</span>
										</div>
										<div class="mobile-stat-item pending">
											<span class="mobile-stat-number"><?= $user['pending_tasks'] ?></span>
											<span class="mobile-stat-label">Pending</span>
										</div>
										<div class="mobile-stat-item progress">
											<span class="mobile-stat-number"><?= $user['in_progress_tasks'] ?></span>
											<span class="mobile-stat-label">In Progress</span>
										</div>
									</div>
									
									<div class="mobile-tasks-container" id="mobile-tasks-<?= $user['id'] ?>">
										<div class="mobile-expand-area">
											<!-- Tasks will be loaded here via AJAX -->
											<div class="no-tasks">Tap the arrow to view tasks...</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>

					<!-- Modal pour ajouter une tâche -->
					<div id="taskModal" class="modal">
						<div class="modal-content">
							<div class="modal-header">
								<h3>🎯 Assign New Task</h3>
							</div>
							<form method="POST" action="app/add-task.php">
								<div class="modal-body">
									<div class="form-group">
										<label>👤 Assign to:</label>
										<input type="text" id="assignedUserName" readonly class="readonly-input">
										<input type="hidden" id="assignedUserId" name="assigned_to">
									</div>

									<div class="form-group">
										<label>📝 Task Name:</label>
										<input type="text" name="task_name" required placeholder="e.g., Prepare monthly financial report">
									</div>

									<div class="form-group">
										<label>📄 Description:</label>
										<textarea name="description" placeholder="Detailed description of the task requirements..."></textarea>
									</div>

									<div class="form-group">
										<label>📅 Deadline:</label>
										<input type="date" name="due_date" required>
									</div>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" onclick="closeTaskModal()">❌ Cancel</button>
									<button type="submit" class="btn btn-primary">✅ Assign Task</button>
								</div>
							</form>
						</div>
					</div>

					<script>
						function filterUsers() {
							const input = document.getElementById('searchInput');
							const filter = input.value.toLowerCase();
							
							// Filter desktop table
							const table = document.getElementById('usersTable');
							const rows = table.getElementsByTagName('tr');

							for (let i = 1; i < rows.length; i += 2) { // Skip every other row (tasks rows)
								const nameCell = rows[i].getElementsByTagName('td')[0];
								const emailCell = rows[i].getElementsByTagName('td')[1];

								if (nameCell && emailCell) {
									const nameText = nameCell.textContent.toLowerCase();
									const emailText = emailCell.textContent.toLowerCase();

									if (nameText.includes(filter) || emailText.includes(filter)) {
										rows[i].style.display = '';
										if (rows[i + 1]) rows[i + 1].style.display = '';
									} else {
										rows[i].style.display = 'none';
										if (rows[i + 1]) rows[i + 1].style.display = 'none';
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

						function toggleTasks(userId) {
							const tasksRow = document.getElementById('tasks-' + userId);
							const expandBtn = document.getElementById('expand-btn-' + userId);
							const userRow = expandBtn.closest('.user-row');

							if (tasksRow.classList.contains('show')) {
								// Fermer
								tasksRow.classList.remove('show');
								expandBtn.classList.remove('expanded');
								userRow.classList.remove('expanded');
							} else {
								// Ouvrir et charger les tâches
								tasksRow.classList.add('show');
								expandBtn.classList.add('expanded');
								userRow.classList.add('expanded');
								loadUserTasks(userId);
							}
						}

						function loadUserTasks(userId) {
							const container = document.getElementById('tasks-container-' + userId);

							// Faire une requête AJAX pour charger les tâches
							fetch('app/get-user-tasks.php?user_id=' + userId)
								.then(response => response.json())
								.then(tasks => {
									if (tasks.length === 0) {
										container.innerHTML = '<div class="no-tasks">No tasks assigned yet</div>';
									} else {
										let tasksHTML = '';
										tasks.forEach(task => {
											tasksHTML += `
                            <div class="task-item" id="task-${task.id}">
                                <div class="task-content">
                                    <div class="task-title">${task.title}</div>
                                    <div class="task-description">${task.description || 'No description'}</div>
                                    <div class="task-meta">
                                        <span class="task-date">Due: ${task.due_date}</span>
                                        <span class="task-status status-${task.status}">${task.status}</span>
                                    </div>
                                </div>
                                <div class="task-actions">
                                    <button class="delete-task-btn" onclick="deleteTask(${task.id}, ${userId})" title="Delete task">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        `;
										});
										container.innerHTML = tasksHTML;
									}
								})
								.catch(error => {
									console.log('Error:', error);
									container.innerHTML = '<div class="no-tasks">Error loading tasks</div>';
								});
						}

						function deleteTask(taskId, userId) {
							if (confirm('Are you sure you want to delete this task?')) {
								fetch('app/delete-task.php', {
										method: 'POST',
										headers: {
											'Content-Type': 'application/x-www-form-urlencoded',
										},
										body: 'task_id=' + taskId
									})
									.then(response => response.json())
									.then(result => {
										if (result.success) {
											// Supprimer visuellement la tâche
											const taskElement = document.getElementById('task-' + taskId);
											if (taskElement) {
												taskElement.remove();
											}

											// Recharger les statistiques en actualisant la page
											setTimeout(() => {
												location.reload();
											}, 500);
										} else {
											alert('Error deleting task: ' + result.message);
										}
									})
									.catch(error => {
										console.log('Delete error:', error);
										alert('Error deleting task');
									});
							}
						}

						function openTaskModal(userId, userName) {
							document.getElementById('assignedUserId').value = userId;
							document.getElementById('assignedUserName').value = userName;
							document.getElementById('taskModal').style.display = 'block';

							// Set minimum date to today
							const today = new Date().toISOString().split('T')[0];
							document.querySelector('input[name="due_date"]').min = today;
						}

						// Mobile version of toggle tasks
						function toggleMobileTasks(userId) {
							const tasksContainer = document.getElementById('mobile-tasks-' + userId);
							const expandBtn = tasksContainer.parentElement.querySelector('.expand-btn');

							if (tasksContainer.classList.contains('show')) {
								// Close
								tasksContainer.classList.remove('show');
								expandBtn.querySelector('i').classList.remove('fa-chevron-up');
								expandBtn.querySelector('i').classList.add('fa-chevron-down');
							} else {
								// Open and load tasks
								tasksContainer.classList.add('show');
								expandBtn.querySelector('i').classList.remove('fa-chevron-down');
								expandBtn.querySelector('i').classList.add('fa-chevron-up');
								loadMobileUserTasks(userId);
							}
						}

						// Load tasks for mobile view
						function loadMobileUserTasks(userId) {
							const container = document.querySelector('#mobile-tasks-' + userId + ' .mobile-expand-area');

							// Make AJAX request to load tasks
							fetch('app/get-user-tasks.php?user_id=' + userId)
								.then(response => response.json())
								.then(tasks => {
									if (tasks.length === 0) {
										container.innerHTML = '<div class="no-tasks">No tasks assigned yet</div>';
									} else {
										let tasksHTML = '<div style="margin-top: 8px;">';
										tasks.forEach(task => {
											const statusClass = task.status === 'completed' ? 'status-completed' : 
															  task.status === 'pending' ? 'status-pending' : 'status-progress';
											tasksHTML += `
												<div class="task-item" style="margin-bottom: 8px; padding: 8px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #667eea;">
													<div style="font-weight: bold; font-size: 13px; color: #2c3e50; margin-bottom: 4px;">${task.title}</div>
													<div style="font-size: 11px; color: #7f8c8d; margin-bottom: 6px;">${task.description}</div>
													<div style="display: flex; justify-content: space-between; align-items: center; font-size: 10px;">
														<span class="${statusClass}" style="padding: 2px 8px; border-radius: 12px; text-transform: uppercase;">${task.status}</span>
														<span style="color: #6c757d;">Due: ${task.due_date}</span>
													</div>
												</div>
											`;
										});
										tasksHTML += '</div>';
										container.innerHTML = tasksHTML;
									}
								})
								.catch(error => {
									console.error('Error loading tasks:', error);
									container.innerHTML = '<div class="no-tasks">Error loading tasks</div>';
								});
						}

						function closeTaskModal() {
							document.getElementById('taskModal').style.display = 'none';
						}

						// Close modal when clicking outside
						window.onclick = function(event) {
							const modal = document.getElementById('taskModal');
							if (event.target == modal) {
								closeTaskModal();
							}
						}
					</script>
				</section>
			</div>

		<?php
	} else {
		// Pour les employés, garde le dashboard existant simple
		?>
			<!DOCTYPE html>
			<html>

			<head>
				<title>Dashboard</title>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
				<link rel="stylesheet" href="css/style.css">
			</head>

			<body>
				<input type="checkbox" id="checkbox">
				<?php include "inc/header.php"; ?>
				<?php include "inc/nav.php"; ?>

				<div class="body">
					<section class="section-1">
						<h4>Welcome Employee Dashboard</h4>
						<!-- Contenu dashboard employé à développer plus tard -->
					</section>
				</div>
			<?php } ?>
			</body>

			</html>
		<?php } else {
		$em = "First login";
		header("Location: login.php?error=$em");
		exit();
	}
		?>