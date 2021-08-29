-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: revisionary_database:3306
-- Generation Time: Aug 29, 2021 at 09:56 PM
-- Server version: 8.0.26
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `revisionaryapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `device_ID` bigint NOT NULL,
  `device_width` mediumint DEFAULT NULL,
  `device_height` mediumint DEFAULT NULL,
  `device_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `device_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `phase_ID` bigint NOT NULL,
  `screen_ID` bigint NOT NULL,
  `user_ID` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_ID` bigint NOT NULL,
  `notification_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `notification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notification_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `object_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_ID` bigint NOT NULL,
  `project_ID` bigint DEFAULT NULL,
  `page_ID` bigint DEFAULT NULL,
  `phase_ID` bigint DEFAULT NULL,
  `device_ID` bigint DEFAULT NULL,
  `pin_ID` bigint DEFAULT NULL,
  `comment_ID` bigint DEFAULT NULL,
  `sender_user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_user_connection`
--

CREATE TABLE `notification_user_connection` (
  `notification_connection_ID` bigint NOT NULL,
  `notification_ID` bigint NOT NULL,
  `user_ID` bigint NOT NULL,
  `notification_read` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_ID` bigint NOT NULL,
  `page_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_url` varchar(2083) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_user` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_pass` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_archived` tinyint(1) NOT NULL DEFAULT '0',
  `page_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `order_number` bigint NOT NULL DEFAULT '0',
  `cat_ID` bigint DEFAULT NULL,
  `project_ID` bigint NOT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages_categories`
--

CREATE TABLE `pages_categories` (
  `cat_ID` bigint NOT NULL,
  `cat_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Category',
  `cat_slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'category',
  `cat_order_number` bigint NOT NULL DEFAULT '0',
  `project_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages_favorites`
--

CREATE TABLE `pages_favorites` (
  `favorite_ID` bigint NOT NULL,
  `page_ID` bigint NOT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `pass_reset_ID` bigint NOT NULL,
  `pass_reset_token` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass_reset_expires` bigint NOT NULL,
  `user_ID` bigint NOT NULL,
  `pass_reset_IP` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phases`
--

CREATE TABLE `phases` (
  `phase_ID` bigint NOT NULL,
  `phase_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'url',
  `phase_internalized` int NOT NULL DEFAULT '0',
  `phase_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_ID` bigint NOT NULL,
  `user_ID` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pins`
--

CREATE TABLE `pins` (
  `pin_ID` bigint NOT NULL,
  `pin_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin_private` tinyint(1) NOT NULL DEFAULT '0',
  `pin_complete` tinyint(1) NOT NULL DEFAULT '0',
  `pin_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pin_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pin_x` decimal(20,5) NOT NULL,
  `pin_y` decimal(20,5) NOT NULL,
  `pin_element_index` bigint NOT NULL,
  `pin_modification_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_modification` varchar(2083) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_modification_original` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_css` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phase_ID` bigint NOT NULL,
  `device_ID` bigint DEFAULT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pin_comments`
--

CREATE TABLE `pin_comments` (
  `comment_ID` bigint NOT NULL,
  `pin_comment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `comment_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pin_ID` bigint NOT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_ID` bigint NOT NULL,
  `project_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `project_archived` tinyint(1) NOT NULL DEFAULT '0',
  `project_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `project_image_device_ID` bigint DEFAULT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects_categories`
--

CREATE TABLE `projects_categories` (
  `cat_ID` bigint NOT NULL,
  `cat_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Category',
  `cat_slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'category',
  `cat_order_number` bigint NOT NULL DEFAULT '0',
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects_favorites`
--

CREATE TABLE `projects_favorites` (
  `favorite_ID` bigint NOT NULL,
  `project_ID` bigint NOT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects_order`
--

CREATE TABLE `projects_order` (
  `order_ID` bigint NOT NULL,
  `order_number` bigint NOT NULL DEFAULT '0',
  `project_ID` bigint NOT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `project_cat_connect`
--

CREATE TABLE `project_cat_connect` (
  `project_cat_connect_ID` bigint NOT NULL,
  `cat_ID` bigint NOT NULL,
  `project_ID` bigint NOT NULL,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `queue_ID` bigint NOT NULL,
  `queue_type` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `queue_object_ID` bigint NOT NULL,
  `queue_PID` bigint DEFAULT NULL,
  `queue_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `queue_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `queue_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `queue_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `screens`
--

CREATE TABLE `screens` (
  `screen_ID` bigint NOT NULL,
  `screen_name` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `screen_width` mediumint NOT NULL,
  `screen_height` mediumint NOT NULL,
  `screen_rotateable` tinyint(1) NOT NULL DEFAULT '0',
  `screen_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_frame` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_cat_ID` bigint NOT NULL,
  `screen_order` bigint NOT NULL,
  `screen_user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `screens`
--

INSERT INTO `screens` (`screen_ID`, `screen_name`, `screen_width`, `screen_height`, `screen_rotateable`, `screen_color`, `screen_frame`, `screen_cat_ID`, `screen_order`, `screen_user_ID`) VALUES
(1, 'iMac 27', 2560, 1440, 0, NULL, NULL, 1, 0, 1),
(2, 'iMac 21', 1920, 1080, 0, NULL, NULL, 1, 1, 1),
(3, 'Macbook Pro 17', 1920, 1200, 0, NULL, NULL, 2, 2, 1),
(4, 'Macbook Pro 15', 1440, 900, 0, NULL, NULL, 2, 3, 1),
(5, 'Macbook Pro 13', 1280, 800, 0, NULL, NULL, 2, 4, 1),
(6, 'iPad', 768, 1024, 1, NULL, NULL, 3, 0, 1),
(7, 'iPhone 6/6S/7/8 Plus', 414, 736, 1, NULL, NULL, 4, 0, 1),
(8, 'iPhone 6/6S/7/8', 375, 667, 1, NULL, NULL, 4, 1, 1),
(9, 'iPhone 5/5C/5S/SE', 320, 568, 1, NULL, NULL, 4, 2, 1),
(10, 'iPhone 4/4S', 320, 480, 1, NULL, NULL, 4, 3, 1),
(11, 'Custom Screen', 1440, 900, 0, NULL, NULL, 5, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `screen_categories`
--

CREATE TABLE `screen_categories` (
  `screen_cat_ID` bigint NOT NULL,
  `screen_cat_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `screen_cat_icon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `screen_cat_order` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `screen_categories`
--

INSERT INTO `screen_categories` (`screen_cat_ID`, `screen_cat_name`, `screen_cat_icon`, `screen_cat_order`) VALUES
(1, 'Desktop', 'fa-desktop', 0),
(2, 'Laptop', 'fa-laptop', 1),
(3, 'Tablet', 'fa-tablet', 2),
(4, 'Mobile', 'fa-mobile', 3),
(5, 'Custom', 'fa-window-maximize', 4);

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `share_ID` bigint NOT NULL,
  `share_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shared_object_ID` bigint NOT NULL,
  `share_to` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sharer_user_ID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` bigint NOT NULL,
  `user_name` varchar(42) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_first_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_last_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_job_title` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_department` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_company` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_picture` varchar(2083) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_has_public_profile` tinyint(1) NOT NULL DEFAULT '0',
  `user_email_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `user_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_IP` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `trial_started_for` smallint DEFAULT NULL,
  `trial_expire_date` timestamp NULL DEFAULT NULL,
  `trial_expired_notified` tinyint(1) NOT NULL DEFAULT '0',
  `user_level_ID` smallint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `user_name`, `user_email`, `user_password`, `user_first_name`, `user_last_name`, `user_job_title`, `user_department`, `user_company`, `user_picture`, `user_has_public_profile`, `user_email_notifications`, `user_registered`, `user_IP`, `trial_started_for`, `trial_expire_date`, `trial_expired_notified`, `user_level_ID`) VALUES
(1, 'bilaltas', 'bilaltas@me.com', '$2y$10$b/jC7podSCVz6yIIKag41.1fa67xvB2utqWWVhogD7C1wkhErLU5C', 'Bilal', 'TAŞ', NULL, NULL, NULL, NULL, 1, 1, '2019-09-23 10:38:14', '127.0.0.1', NULL, NULL, 0, 1),
(2, 'bill-tas', 'bilalltas@gmail.com', '$2y$10$d39kS46ZfdS9x4Vhzqp3mebAq17UD31qLl6ogC6RPHzwM1bG0.0hK', 'Bill', 'TAS', 'Lead Web Developer', 'Web Department', 'Invicti', NULL, 0, 1, '2019-09-23 10:38:14', '127.0.0.1', NULL, '2020-01-18 22:48:26', 1, 4),
(3, 'cuneyt-tas', 'cuneyt@twelve12.com', '$2y$10$hmbLL2pKTuBa7MtUEC/vtu2LcCggurulno24xAa9fXkerZSr49EIq', 'Cüneyt', 'TAŞ', 'Web Developer', 'Web Department', 'Rewire Security', NULL, 0, 1, '2019-09-23 10:38:14', '127.0.0.1', NULL, NULL, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE `user_levels` (
  `user_level_ID` smallint NOT NULL,
  `user_level_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_level_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_level_max_project` int NOT NULL,
  `user_level_max_page` int NOT NULL,
  `user_level_max_screen` int NOT NULL,
  `user_level_max_content_pin` int NOT NULL,
  `user_level_max_comment_pin` int NOT NULL,
  `user_level_max_client` int NOT NULL,
  `user_level_max_load` int NOT NULL,
  `user_level_price` float NOT NULL,
  `user_level_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_levels`
--

INSERT INTO `user_levels` (`user_level_ID`, `user_level_name`, `user_level_description`, `user_level_max_project`, `user_level_max_page`, `user_level_max_screen`, `user_level_max_content_pin`, `user_level_max_comment_pin`, `user_level_max_client`, `user_level_max_load`, `user_level_price`, `user_level_color`) VALUES
(1, 'Admin', 'Admin Description', 99999, 99999, 99999, 99999, 99999, 99999, 99999, 99999, 'black'),
(2, 'Free', 'Free for all users', 3, 6, 10, 30, 50, 0, 20, 0, 'black'),
(3, 'Plus', 'Plus description', 12, 24, 40, 120, 99999, 3, 120, 29, 'green'),
(4, 'Enterprise', 'Enterprise description.', 99999, 99999, 99999, 99999, 99999, 99999, 2048, 99, 'gold');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_ID`),
  ADD KEY `phase_ID` (`phase_ID`),
  ADD KEY `user_ID` (`user_ID`),
  ADD KEY `screen_ID` (`screen_ID`) USING BTREE;

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_ID`),
  ADD KEY `sender_user_ID` (`sender_user_ID`),
  ADD KEY `page_ID` (`page_ID`),
  ADD KEY `phase_ID` (`phase_ID`),
  ADD KEY `device_ID` (`device_ID`),
  ADD KEY `pin_ID` (`pin_ID`),
  ADD KEY `project_ID` (`project_ID`),
  ADD KEY `comment_ID` (`comment_ID`);

--
-- Indexes for table `notification_user_connection`
--
ALTER TABLE `notification_user_connection`
  ADD PRIMARY KEY (`notification_connection_ID`),
  ADD KEY `notification_ID` (`notification_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_ID`),
  ADD KEY `user_ID` (`user_ID`) USING BTREE,
  ADD KEY `project_ID` (`project_ID`) USING BTREE,
  ADD KEY `cat_ID` (`cat_ID`);

--
-- Indexes for table `pages_categories`
--
ALTER TABLE `pages_categories`
  ADD PRIMARY KEY (`cat_ID`),
  ADD KEY `project_ID` (`project_ID`);

--
-- Indexes for table `pages_favorites`
--
ALTER TABLE `pages_favorites`
  ADD PRIMARY KEY (`favorite_ID`),
  ADD KEY `page_ID` (`page_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`pass_reset_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `phases`
--
ALTER TABLE `phases`
  ADD PRIMARY KEY (`phase_ID`),
  ADD KEY `page_ID` (`page_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `pins`
--
ALTER TABLE `pins`
  ADD PRIMARY KEY (`pin_ID`),
  ADD KEY `user_ID` (`user_ID`),
  ADD KEY `phase_ID` (`phase_ID`),
  ADD KEY `device_ID` (`device_ID`);

--
-- Indexes for table `pin_comments`
--
ALTER TABLE `pin_comments`
  ADD PRIMARY KEY (`comment_ID`),
  ADD KEY `pin_ID` (`pin_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_ID`),
  ADD KEY `projects_ibfk_3` (`user_ID`),
  ADD KEY `project_image_device_ID` (`project_image_device_ID`);

--
-- Indexes for table `projects_categories`
--
ALTER TABLE `projects_categories`
  ADD PRIMARY KEY (`cat_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `projects_favorites`
--
ALTER TABLE `projects_favorites`
  ADD PRIMARY KEY (`favorite_ID`),
  ADD KEY `project_ID` (`project_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `projects_order`
--
ALTER TABLE `projects_order`
  ADD PRIMARY KEY (`order_ID`),
  ADD KEY `sorting_ibfk_1` (`user_ID`),
  ADD KEY `project_ID` (`project_ID`);

--
-- Indexes for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  ADD PRIMARY KEY (`project_cat_connect_ID`),
  ADD KEY `cat_ID` (`cat_ID`),
  ADD KEY `project_ID` (`project_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`queue_ID`);

--
-- Indexes for table `screens`
--
ALTER TABLE `screens`
  ADD PRIMARY KEY (`screen_ID`) USING BTREE,
  ADD KEY `screen_cat_ID` (`screen_cat_ID`) USING BTREE,
  ADD KEY `screen_user_ID` (`screen_user_ID`) USING BTREE;

--
-- Indexes for table `screen_categories`
--
ALTER TABLE `screen_categories`
  ADD PRIMARY KEY (`screen_cat_ID`) USING BTREE;

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`share_ID`),
  ADD KEY `shares_ibfk_1` (`sharer_user_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD KEY `user_level_ID` (`user_level_ID`),
  ADD KEY `trial_started_for` (`trial_started_for`);

--
-- Indexes for table `user_levels`
--
ALTER TABLE `user_levels`
  ADD PRIMARY KEY (`user_level_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `device_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_user_connection`
--
ALTER TABLE `notification_user_connection`
  MODIFY `notification_connection_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages_categories`
--
ALTER TABLE `pages_categories`
  MODIFY `cat_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages_favorites`
--
ALTER TABLE `pages_favorites`
  MODIFY `favorite_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `pass_reset_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phases`
--
ALTER TABLE `phases`
  MODIFY `phase_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pins`
--
ALTER TABLE `pins`
  MODIFY `pin_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pin_comments`
--
ALTER TABLE `pin_comments`
  MODIFY `comment_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects_categories`
--
ALTER TABLE `projects_categories`
  MODIFY `cat_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects_favorites`
--
ALTER TABLE `projects_favorites`
  MODIFY `favorite_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects_order`
--
ALTER TABLE `projects_order`
  MODIFY `order_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  MODIFY `project_cat_connect_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `queue_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `screens`
--
ALTER TABLE `screens`
  MODIFY `screen_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `screen_categories`
--
ALTER TABLE `screen_categories`
  MODIFY `screen_cat_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_levels`
--
ALTER TABLE `user_levels`
  MODIFY `user_level_ID` smallint NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`screen_ID`) REFERENCES `screens` (`screen_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `devices_ibfk_3` FOREIGN KEY (`phase_ID`) REFERENCES `phases` (`phase_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `devices_ibfk_4` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sender_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`device_ID`) REFERENCES `devices` (`device_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`page_ID`) REFERENCES `pages` (`page_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`phase_ID`) REFERENCES `phases` (`phase_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifications_ibfk_5` FOREIGN KEY (`device_ID`) REFERENCES `devices` (`device_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifications_ibfk_6` FOREIGN KEY (`pin_ID`) REFERENCES `pins` (`pin_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifications_ibfk_7` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifications_ibfk_8` FOREIGN KEY (`comment_ID`) REFERENCES `pin_comments` (`comment_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `notification_user_connection`
--
ALTER TABLE `notification_user_connection`
  ADD CONSTRAINT `notification_user_connection_ibfk_1` FOREIGN KEY (`notification_ID`) REFERENCES `notifications` (`notification_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `notification_user_connection_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `pages_ibfk_2` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `pages_ibfk_3` FOREIGN KEY (`cat_ID`) REFERENCES `pages_categories` (`cat_ID`) ON DELETE SET NULL;

--
-- Constraints for table `pages_categories`
--
ALTER TABLE `pages_categories`
  ADD CONSTRAINT `pages_categories_ibfk_1` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `pages_favorites`
--
ALTER TABLE `pages_favorites`
  ADD CONSTRAINT `pages_favorites_ibfk_1` FOREIGN KEY (`page_ID`) REFERENCES `pages` (`page_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `pages_favorites_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `phases`
--
ALTER TABLE `phases`
  ADD CONSTRAINT `phases_ibfk_1` FOREIGN KEY (`page_ID`) REFERENCES `pages` (`page_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `phases_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `pins`
--
ALTER TABLE `pins`
  ADD CONSTRAINT `pins_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `pins_ibfk_2` FOREIGN KEY (`phase_ID`) REFERENCES `phases` (`phase_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `pins_ibfk_3` FOREIGN KEY (`device_ID`) REFERENCES `devices` (`device_ID`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `pin_comments`
--
ALTER TABLE `pin_comments`
  ADD CONSTRAINT `pin_comments_ibfk_1` FOREIGN KEY (`pin_ID`) REFERENCES `pins` (`pin_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `pin_comments_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_ibfk_4` FOREIGN KEY (`project_image_device_ID`) REFERENCES `devices` (`device_ID`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `projects_categories`
--
ALTER TABLE `projects_categories`
  ADD CONSTRAINT `projects_categories_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `projects_favorites`
--
ALTER TABLE `projects_favorites`
  ADD CONSTRAINT `projects_favorites_ibfk_1` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `projects_favorites_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `projects_order`
--
ALTER TABLE `projects_order`
  ADD CONSTRAINT `projects_order_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_order_ibfk_2` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  ADD CONSTRAINT `project_cat_connect_ibfk_1` FOREIGN KEY (`cat_ID`) REFERENCES `projects_categories` (`cat_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `project_cat_connect_ibfk_2` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `project_cat_connect_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `screens`
--
ALTER TABLE `screens`
  ADD CONSTRAINT `screens_ibfk_1` FOREIGN KEY (`screen_cat_ID`) REFERENCES `screen_categories` (`screen_cat_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `screens_ibfk_2` FOREIGN KEY (`screen_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`sharer_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_level_ID`) REFERENCES `user_levels` (`user_level_ID`) ON DELETE SET NULL ON UPDATE RESTRICT,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`trial_started_for`) REFERENCES `user_levels` (`user_level_ID`) ON DELETE SET NULL ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
