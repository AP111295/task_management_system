# Task Management System - Complete Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [Security Features](#security-features)
3. [Installation Guide](#installation-guide)
4. [Database Schema](#database-schema)
5. [API Documentation](#api-documentation)
6. [User Manual](#user-manual)
7. [Security Guidelines](#security-guidelines)
8. [Troubleshooting](#troubleshooting)

## Project Overview

### Description
A comprehensive task management system built with PHP and MySQL, featuring role-based access control, secure authentication, and modern responsive design.

### Features
- **CRUD Operations**: Complete Create, Read, Update, Delete functionality
- **XSS Protection**: All outputs sanitized with htmlspecialchars()
- **SQL Injection Protection**: Prepared statements for all database operations
- **CSRF Protection**: Token-based form protection
- **Brute Force Protection**: Account lockout after failed login attempts
- **CAPTCHA**: Mathematical challenge for login security
- **Email Verification**: Account activation via email
- **Password Security**: Secure hashing with PHP's password_hash()
- **MVC Architecture**: Organized code structure
- **Responsive Design**: Mobile-first approach

### Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Email**: PHPMailer
- **Security**: Session management, CSRF tokens, input validation

## Security Features

### 1. XSS (Cross-Site Scripting) Protection
```php
// All user inputs are sanitized before display
echo htmlspecialchars($user_input);
```

### 2. SQL Injection Protection
```php
// Using prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
```

### 3. CSRF (Cross-Site Request Forgery) Protection
```php
// Generate CSRF token
$token = generate_csrf_token();

// Validate CSRF token
verify_csrf_token();
```

### 4. Brute Force Protection
- Account lockout after 5 failed attempts
- 15-minute lockout duration
- IP-based tracking
- Automatic cleanup of old attempts

### 5. Password Security
```php
// Hashing passwords
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Verifying passwords
password_verify($password, $hashed);
```

## Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (for dependencies)

### Step 1: Download and Setup
```bash
# Clone or download the project
git clone [repository-url]
cd task_management_system

# Install dependencies
composer install
```

### Step 2: Database Setup
```sql
-- Create database
CREATE DATABASE task_management_db;

-- Import main schema
mysql -u root -p task_management_db < Db.sql

-- Import security enhancements
mysql -u root -p task_management_db < security_schema.sql
```

### Step 3: Configuration
```php
// Update DB_connection.php
$sName = "localhost";
$uName = "your_username";
$pass  = "your_password";
$db_name = "task_management_db";
```

### Step 4: Email Configuration
```php
// Update app/email_verification.php
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
```

### Step 5: Permissions
```bash
# Set proper permissions
chmod 755 uploads/
chmod 755 uploads/profiles/
```

## Database Schema

### Core Tables

#### users
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    profile_image VARCHAR(255),
    avatar_letter CHAR(1) DEFAULT 'U',
    avatar_color VARCHAR(7) DEFAULT '#f39c12',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### tasks
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    assigned_to INT,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);
```

#### Security Tables

#### login_attempts
```sql
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### email_verification
```sql
CREATE TABLE email_verification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## API Documentation

### Authentication Endpoints

#### Login
```
POST /app/login.php
Content-Type: application/x-www-form-urlencoded

Parameters:
- username (string, required)
- password (string, required)
- captcha_answer (integer, required)
- csrf_token (string, required)

Response:
- Success: Redirect to index.php
- Error: Redirect to login.php with error message
```

#### Logout
```
GET /logout.php

Response:
- Redirect to login.php
```

### User Management Endpoints

#### Create User
```
POST /app/add-user.php
Content-Type: application/x-www-form-urlencoded

Parameters:
- full_name (string, required)
- email (string, required)
- username (string, required)
- password (string, required)
- csrf_token (string, required)

Response:
- Success: Redirect with success message
- Error: Redirect with error message
```

#### Update User
```
POST /app/update-user.php
Content-Type: application/x-www-form-urlencoded

Parameters:
- id (integer, required)
- full_name (string, required)
- email (string, required)
- username (string, required)
- csrf_token (string, required)

Response:
- Success: Redirect with success message
- Error: Redirect with error message
```

### Task Management Endpoints

#### Create Task
```
POST /app/add-task.php
Content-Type: application/x-www-form-urlencoded

Parameters:
- task_name (string, required)
- description (string, optional)
- assigned_to (integer, required)
- due_date (date, required)
- csrf_token (string, required)

Response:
- Success: Redirect to index.php
- Error: Redirect with error message
```

#### Get User Tasks
```
GET /app/get-user-tasks.php?user_id={id}

Response:
Content-Type: application/json
[
    {
        "id": 1,
        "title": "Task Title",
        "description": "Task Description",
        "status": "pending",
        "due_date": "2025-01-15"
    }
]
```

## User Manual

### Admin Users

#### Dashboard Overview
- View all employees and their task statistics
- Assign new tasks to employees
- Monitor task completion rates
- Access user management features

#### User Management
1. **Add New User**
   - Navigate to user management
   - Click "Add New User"
   - Fill in user details
   - User receives email verification

2. **Edit User**
   - Click edit button next to user
   - Modify user information
   - Save changes

3. **Delete User**
   - Click delete button
   - Confirm deletion
   - All user's tasks are also deleted

#### Task Management
1. **Assign Task**
   - Click "Assign Task" button
   - Select employee
   - Enter task details and deadline
   - Submit task

2. **View Tasks**
   - Click on employee row to expand
   - View all assigned tasks
   - See task status and details

### Employee Users

#### Profile Management
1. **Update Profile**
   - Go to profile page
   - Edit personal information
   - Change password
   - Upload profile picture

2. **Change Password**
   - Enter current password
   - Enter new password
   - Confirm new password
   - Submit changes

### General Features

#### Login Process
1. Enter username and password
2. Solve CAPTCHA challenge
3. Click login button
4. Access dashboard

#### Email Verification
1. Check email after registration
2. Click verification link
3. Account is activated
4. Login with credentials

## Security Guidelines

### For Administrators

#### Password Policy
- Minimum 8 characters
- Include uppercase, lowercase, numbers
- Change passwords regularly
- Don't share credentials

#### Account Security
- Monitor failed login attempts
- Regular security audits
- Keep system updated
- Backup database regularly

#### Data Protection
- Regular backups
- Secure file permissions
- Monitor access logs
- Implement HTTPS

### For Developers

#### Input Validation
```php
// Always validate and sanitize input
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
```

#### Database Operations
```php
// Use prepared statements
$stmt = $conn->prepare("INSERT INTO table (column) VALUES (?)");
$stmt->execute([$value]);
```

#### Session Security
```php
// Regenerate session ID
session_regenerate_id(true);

// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
```

## Troubleshooting

### Common Issues

#### Database Connection Error
```
Error: Connection failed: SQLSTATE[HY000] [1045] Access denied
Solution: Check database credentials in DB_connection.php
```

#### Email Not Sending
```
Error: Email verification failed
Solution: 
1. Check email configuration in email_verification.php
2. Verify SMTP settings
3. Enable "Less secure app access" for Gmail
```

#### CSRF Token Error
```
Error: CSRF token validation failed
Solution: 
1. Ensure forms include csrf_input()
2. Check session configuration
3. Verify token generation
```

#### Account Locked
```
Error: Account temporarily locked
Solution: 
1. Wait 15 minutes
2. Or manually clear login_attempts table
3. Check IP address restrictions
```

### Maintenance Tasks

#### Clean Old Data
```sql
-- Clean old login attempts
DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Clean expired verification tokens
DELETE FROM email_verification WHERE expires_at < NOW();
```

#### Monitor Security
```sql
-- Check failed login attempts
SELECT username, ip_address, COUNT(*) as attempts 
FROM login_attempts 
WHERE attempt_time > DATE_SUB(NOW(), INTERVAL 1 DAY) 
GROUP BY username, ip_address 
ORDER BY attempts DESC;
```

### System Requirements

#### Minimum Requirements
- PHP 7.4+
- MySQL 5.7+
- 100MB disk space
- 512MB RAM

#### Recommended Requirements
- PHP 8.0+
- MySQL 8.0+
- 500MB disk space
- 1GB RAM
- SSL certificate

### Support

For technical support:
1. Check this documentation
2. Review error logs
3. Verify configuration
4. Contact system administrator

---

**Version**: 1.0  
**Last Updated**: October 2025  
**Author**: Task Master Development Team