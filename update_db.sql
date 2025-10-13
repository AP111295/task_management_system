-- Add profile image fields to users table
ALTER TABLE users 
ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL,
ADD COLUMN avatar_letter CHAR(1) DEFAULT 'U',
ADD COLUMN avatar_color VARCHAR(7) DEFAULT '#f39c12';