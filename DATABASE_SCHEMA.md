# Task Management System - Database Schema (MCD)

## Entity-Relationship Diagram

```
┌─────────────────┐    ┌──────────────────┐    ┌────────────────────┐
│      USERS      │    │      TASKS       │    │   NOTIFICATIONS    │
├─────────────────┤    ├──────────────────┤    ├────────────────────┤
│ id (PK)         │    │ id (PK)          │    │ id (PK)            │
│ full_name       │    │ title            │    │ user_id            │
│ username        │    │ description      │    │ username           │
│ password        │◄───┤ assigned_to (FK) │    │ email              │
│ role            │    │ due_date         │    │ reason             │
│ email           │    │ status           │    │ status             │
│ profile_image   │    │ created_at       │    │ created_at         │
│ avatar_letter   │    └──────────────────┘    └────────────────────┘
│ avatar_color    │    
└─────────────────┘    ┌─────────────────────┐
         │              │  LOGIN_ATTEMPTS     │
         │              ├─────────────────────┤
         │              │ id (PK)             │
         │              │ username            │
         └──────────────┤ ip_address          │
                        │ attempt_time        │
                        └─────────────────────┘

Note: EMAIL_VERIFICATION and PASSWORD_RESET_TOKENS tables 
      are referenced in code but DO NOT EXIST in actual database
```

## Core Tables

### 1. USERS Table
**Purpose**: Stores all user information and authentication data

| Column Name     | Data Type                 | Constraints                 | Description                    |
|----------------|---------------------------|----------------------------|--------------------------------|
| id             | INT(11)                   | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier         |
| full_name      | VARCHAR(50)               | NOT NULL                    | User's complete name           |
| username       | VARCHAR(50)               | NOT NULL                    | Login username                 |
| password       | VARCHAR(255)              | NOT NULL                    | Hashed password                |
| role           | ENUM('admin','employee')  | NOT NULL                    | User access level              |
| email          | VARCHAR(100)              | NOT NULL                    | Email address                  |
| profile_image  | VARCHAR(255)              | NULL                        | Profile picture filename       |
| avatar_letter  | CHAR(1)                   | DEFAULT 'U'                 | Avatar letter                  |
| avatar_color   | VARCHAR(7)                | DEFAULT '#f39c12'           | Avatar background color        |

**Indexes**:
- PRIMARY KEY (id)
- ⚠️ **MISSING**: UNIQUE constraints on email and username should be added

**Note**: The `email_verified` and `created_at` columns mentioned in code are NOT implemented in the actual database

### 2. TASKS Table
**Purpose**: Stores task information and assignments

| Column Name  | Data Type      | Constraints                    | Description                    |
|-------------|----------------|--------------------------------|--------------------------------|
| id          | INT            | PRIMARY KEY, AUTO_INCREMENT    | Unique task identifier         |
| title       | VARCHAR(100)   | NOT NULL                       | Task title                     |
| description | TEXT           | NULL                           | Detailed task description      |
| assigned_to | INT            | FOREIGN KEY → users(id)        | User assigned to task          |
| status      | ENUM           | ('pending','in_progress','completed') | Task completion status |
| due_date    | DATE           | NULL                           | Task deadline                  |
| created_at  | TIMESTAMP      | DEFAULT CURRENT_TIMESTAMP      | Task creation date             |

**Relationships**:
- FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE CASCADE

**Indexes**:
- PRIMARY KEY (id)
- INDEX (assigned_to)
- INDEX (status)
- INDEX (due_date)

### 3. NOTIFICATIONS Table
**Purpose**: Stores system notifications and messages

| Column Name | Data Type      | Constraints                    | Description                    |
|------------|----------------|--------------------------------|--------------------------------|
| id         | INT(11)        | PRIMARY KEY, AUTO_INCREMENT    | Unique notification identifier |
| user_id    | INT(11)        | NULL                           | Associated user ID             |
| username   | VARCHAR(255)   | NULL                           | Username for notification      |
| email      | VARCHAR(255)   | NULL                           | Email for notification         |
| reason     | VARCHAR(255)   | NULL                           | Reason/type of notification    |
| status     | VARCHAR(50)    | DEFAULT 'pending'              | Processing status              |
| created_at | TIMESTAMP      | DEFAULT CURRENT_TIMESTAMP      | Creation timestamp             |

**Indexes**:
- PRIMARY KEY (id)

**Note**: This table structure differs significantly from typical notification patterns and may need refactoring

## Security Tables

### 4. LOGIN_ATTEMPTS Table
**Purpose**: Tracks failed login attempts for brute force protection

| Column Name   | Data Type      | Constraints                    | Description                    |
|--------------|----------------|--------------------------------|--------------------------------|
| id           | INT            | PRIMARY KEY, AUTO_INCREMENT    | Unique attempt identifier      |
| username     | VARCHAR(50)    | NULL                           | Attempted username             |
| ip_address   | VARCHAR(45)    | NOT NULL                       | Source IP address              |
| attempt_time | TIMESTAMP      | DEFAULT CURRENT_TIMESTAMP      | Time of attempt                |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (username, attempt_time)
- INDEX (ip_address, attempt_time)

## ⚠️ MISSING TABLES (Referenced in Code but NOT in Database)

The following tables are referenced in your security code but **DO NOT EXIST** in your actual database:

### EMAIL_VERIFICATION Table (MISSING)
**Should contain**: id, user_id, token, created_at, expires_at, is_used
**Referenced in**: `app/email_verification.php`
**Status**: ❌ Table does not exist - email verification will fail

### PASSWORD_RESET_TOKENS Table (MISSING) 
**Should contain**: id, user_id, token, created_at, expires_at, is_used
**Referenced in**: Password reset functionality
**Status**: ❌ Table does not exist - password reset may use different mechanism

## Relationships Summary

### Actual Relationships (Based on Existing Database)

1. **USERS → TASKS**
   - One user can be assigned multiple tasks
   - Foreign Key: tasks.assigned_to → users.id
   - ✅ **EXISTS**: Properly implemented with foreign key constraint

2. **USERS ↔ NOTIFICATIONS** 
   - ⚠️ **UNCLEAR**: No formal foreign key relationship
   - Current structure stores user_id, username, email separately
   - ❌ **NEEDS REVIEW**: Should be refactored for proper relationships

3. **USERS ↔ LOGIN_ATTEMPTS**
   - ⚠️ **LOOSE RELATIONSHIP**: Connected by username (not user_id)
   - No formal foreign key constraint
   - ✅ **FUNCTIONAL**: Works for brute force protection
   - One user can receive multiple notifications
   - Foreign Key: notifications.recipient → users.id
   - Cascade: DELETE CASCADE

3. **USERS → EMAIL_VERIFICATION**
   - One user can have multiple verification tokens (though only one active)
   - Foreign Key: email_verification.user_id → users.id
   - Cascade: DELETE CASCADE

4. **USERS → PASSWORD_RESET_TOKENS**
   - One user can have multiple reset tokens (though only one active)
   - Foreign Key: password_reset_tokens.user_id → users.id
   - Cascade: DELETE CASCADE

5. **USERS → LOGIN_ATTEMPTS**
   - One user can have multiple login attempts tracked
   - No formal foreign key (username field can be NULL for IP-only tracking)

## Data Integrity Rules

### Primary Keys
- All tables have auto-incrementing integer primary keys
- Ensures unique identification of each record

### Foreign Key Constraints
- All foreign keys use CASCADE DELETE to maintain referential integrity
- Prevents orphaned records when parent records are deleted

### Data Validation
- ENUM constraints ensure valid status values
- NOT NULL constraints prevent missing critical data
- UNIQUE constraints prevent duplicate usernames/emails

### Security Measures
- Passwords are hashed using PHP's password_hash() function
- Tokens use cryptographically secure random generation
- Timestamps track creation and expiration for security tokens

## 🚨 CRITICAL ISSUES TO ADDRESS

### 1. **Missing Security Tables**
Your security features will NOT work because these tables don't exist:
```sql
-- Create these tables to fix security features:
CREATE TABLE email_verification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 2. **Missing Constraints**
Add these constraints for data integrity:
```sql
-- Add unique constraints to prevent duplicate users
ALTER TABLE users ADD UNIQUE (email);
ALTER TABLE users ADD UNIQUE (username);

-- Add missing columns for email verification
ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

### 3. **Notifications Table Issues**
Your notifications table structure is unusual and may cause issues:
- No proper foreign key to users table
- Stores redundant user information (username, email)
- Missing essential fields (message, type, is_read)

## 📊 Current vs Expected Schema Comparison

| Feature | Code Expects | Database Has | Status |
|---------|-------------|--------------|---------|
| Email Verification | ✅ Full table | ❌ No table | 🔴 BROKEN |
| Password Reset | ✅ Full table | ❌ No table | 🔴 BROKEN |
| User Email Unique | ✅ UNIQUE constraint | ❌ No constraint | 🟡 RISKY |
| User Username Unique | ✅ UNIQUE constraint | ❌ No constraint | 🟡 RISKY |
| Email Verified Flag | ✅ Boolean column | ❌ No column | 🔴 BROKEN |
| User Created Date | ✅ Timestamp | ❌ No column | 🟡 MISSING |
| Notifications FK | ✅ Foreign key | ❌ No proper FK | 🟡 SUBOPTIMAL |

## 🔧 Quick Fix Commands

Run these SQL commands to align your database with your code:

```sql
-- Fix users table
ALTER TABLE users ADD UNIQUE (email);
ALTER TABLE users ADD UNIQUE (username);
ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT FALSE AFTER email;
ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER avatar_color;

-- Create missing security tables
CREATE TABLE email_verification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Fix notifications table (optional - requires code changes)
-- This would require updating your PHP code as well
```

## ✅ What Actually Works

Based on your current database:
- ✅ **User Management**: Basic CRUD operations
- ✅ **Task Management**: Full functionality with proper foreign keys
- ✅ **Login Attempts**: Brute force protection works
- ✅ **Basic Authentication**: Login/logout works
- ❌ **Email Verification**: Will fail (missing table)
- ❌ **Password Reset**: Will fail (missing table)
- ⚠️ **Notifications**: Works but structure is unconventional

**RECOMMENDATION**: Run the SQL fixes above to make your database match your code expectations.