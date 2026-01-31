-- Add missing columns for scheduling and replies
-- Run this in phpMyAdmin or MySQL

-- Add scheduled_at and post_data_json to posts table
ALTER TABLE `posts` 
ADD COLUMN `scheduled_at` DATETIME NULL AFTER `status`,
ADD COLUMN `post_data_json` TEXT NULL AFTER `scheduled_at`;

-- Add replied_at to reviews table
ALTER TABLE `reviews` 
ADD COLUMN `replied_at` DATETIME NULL AFTER `reply_text`;

-- Update posts status enum-like values
-- Status can be: LIVE, SCHEDULED, LOCAL, FAILED
