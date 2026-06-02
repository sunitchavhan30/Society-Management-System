-- Society Complaint Management System SQL export
-- Run this file in MySQL to create the schema and seed default accounts.

CREATE DATABASE IF NOT EXISTS `society_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `society_db`;

DROP TABLE IF EXISTS `feedbacks`;
DROP TABLE IF EXISTS `complaints`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('resident','staff','admin') NOT NULL DEFAULT 'resident',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `complaints` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `description` TEXT NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `status` ENUM('Open','In Progress','Resolved','Closed') NOT NULL DEFAULT 'Open',
  `assigned_to` INT DEFAULT NULL,
  `staff_note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`resident_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `feedbacks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_id` INT NOT NULL,
  `resident_id` INT NOT NULL,
  `rating` TINYINT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`resident_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
  ('Admin User', 'admin@domain.com', '$2y$10$JNrbavlBnQYdwFQ5NSO.1.HWvG/xd7w18KFUQhpBa3btcERQQ2x5e', 'admin'),
  ('Staff User', 'staff@domain.com', '$2y$10$VutFyNHyU8pCiqqrUzcwJeE3q56MvyqqLiEYUHQcy34SpVTfZeM9e', 'staff');
