-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2022 at 03:02 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloud_monster`
--

-- --------------------------------------------------------

--
-- Table structure for table `buckets`
--

CREATE TABLE `buckets` (
  `id` int(11) NOT NULL,
  `folderId` int(11) DEFAULT 1,
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `mime` varchar(25) COLLATE utf8mb4_bin DEFAULT NULL,
  `size` bigint(20) DEFAULT 0,
  `ext` varchar(6) COLLATE utf8mb4_bin DEFAULT NULL,
  `tmp` text COLLATE utf8mb4_bin DEFAULT NULL,
  `shared` tinyint(4) DEFAULT 1,
  `autoReUploadSession` tinyint(4) DEFAULT 0,
  `link` text COLLATE utf8mb4_bin DEFAULT NULL,
  `isProcessing` tinyint(4) DEFAULT 0,
  `uniqId` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `cloud_drives`
--

CREATE TABLE `cloud_drives` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `type` varchar(75) COLLATE utf8mb4_bin NOT NULL,
  `authData` text COLLATE utf8mb4_bin DEFAULT NULL,
  `has_access` tinyint(4) DEFAULT 0 COMMENT 'has access = 1,\r\nno access = 0',
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0 COMMENT '0 = active,\r\n1 = paused,\r\n2 = broken'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `cloud_folders`
--

CREATE TABLE `cloud_folders` (
  `id` int(11) NOT NULL,
  `localFolderId` int(11) NOT NULL,
  `cloudDriveId` int(11) NOT NULL,
  `code` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `bucketId` int(11) NOT NULL,
  `cloudDriveId` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `sharedLink` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `msg` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `isUsed` tinyint(4) DEFAULT 0,
  `slug` varchar(15) COLLATE utf8mb4_bin NOT NULL,
  `lastCheckedAt` timestamp NULL DEFAULT current_timestamp(),
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp(),
  `pstatus` tinyint(4) DEFAULT 0 COMMENT '0 = inactive,\r\n1 = active,\r\n2 = waiting,\r\n3 = processing\r\n',
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `local_folders`
--

CREATE TABLE `local_folders` (
  `id` int(11) NOT NULL,
  `parentId` int(11) NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `tmp` text COLLATE utf8mb4_bin DEFAULT NULL,
  `isLocked` tinyint(4) DEFAULT 0,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `local_folders`
--

INSERT INTO `local_folders` (`id`, `parentId`, `name`, `tmp`, `isLocked`, `createdAt`, `updatedAt`, `status`) VALUES
(1, 0, 'root', NULL, 0, '2021-11-22 06:33:26', '2021-11-22 06:33:26', 0);

-- --------------------------------------------------------

--
-- Table structure for table `process_tracker`
--

CREATE TABLE `process_tracker` (
  `id` int(11) NOT NULL,
  `fileId` int(11) NOT NULL,
  `callerId` varchar(11) COLLATE utf8mb4_bin NOT NULL,
  `progress` tinyint(4) DEFAULT 0,
  `currentSpeed` varchar(11) COLLATE utf8mb4_bin DEFAULT NULL,
  `avgSpeed` varchar(11) COLLATE utf8mb4_bin DEFAULT NULL,
  `processTime` smallint(6) DEFAULT 0,
  `remainingTime` smallint(6) DEFAULT 0,
  `isTracking` tinyint(4) DEFAULT 0,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `config` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `var` text COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`config`, `var`) VALUES
('version', '1.1'),
('max_upload_process', '2'),
('custom_slugs', '{\"file\":\"\",\"bucket\":\"\"}'),
('file_check_time', '24'),
('file_auto_re_upload', '0'),
('is_visit_info_required', '0'),
('blacklisted_ips', '[]'),
('blacklisted_countries', '[]'),
('blocked_requests', '0'),
('analytics_system', '1'),
('upload_chunk_size', '5'),
('file_op_rename', '1'),
('folder_op_create', '1'),
('folder_op_rename', '1'),
('folder_op_move', '1'),
('file_check_time', '24'),
('file_op_move', '1'),
('login_username', 'monster'),
('login_password', '$2y$10$gyfUcRl32xbw6Yo1mpWLBuwqBhnBPwU0WStN0ssSb5bICzbVRp0NS'),
('login_remember_token', 'g9Le4tGC0khX'),
('real_monster_name', 'John Antonio');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `fileId` int(11) NOT NULL,
  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `countryCode` varchar(3) COLLATE utf8mb4_bin NOT NULL,
  `visit` int(11) DEFAULT 1,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buckets`
--
ALTER TABLE `buckets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folderId` (`folderId`);

--
-- Indexes for table `cloud_drives`
--
ALTER TABLE `cloud_drives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cloud_folders`
--
ALTER TABLE `cloud_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cloud_folder_uniq` (`localFolderId`,`cloudDriveId`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `files_unique_index` (`bucketId`,`cloudDriveId`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `cloudDriveId` (`cloudDriveId`);

--
-- Indexes for table `local_folders`
--
ALTER TABLE `local_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folder_name_uniq` (`parentId`,`name`);

--
-- Indexes for table `process_tracker`
--
ALTER TABLE `process_tracker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fileId` (`fileId`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fileId` (`fileId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buckets`
--
ALTER TABLE `buckets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cloud_drives`
--
ALTER TABLE `cloud_drives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cloud_folders`
--
ALTER TABLE `cloud_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `local_folders`
--
ALTER TABLE `local_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `process_tracker`
--
ALTER TABLE `process_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buckets`
--
ALTER TABLE `buckets`
  ADD CONSTRAINT `buckets_ibfk_1` FOREIGN KEY (`folderId`) REFERENCES `local_folders` (`id`);

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`bucketId`) REFERENCES `buckets` (`id`),
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`cloudDriveId`) REFERENCES `cloud_drives` (`id`);

--
-- Constraints for table `process_tracker`
--
ALTER TABLE `process_tracker`
  ADD CONSTRAINT `process_tracker_ibfk_1` FOREIGN KEY (`fileId`) REFERENCES `files` (`id`);

--
-- Constraints for table `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`fileId`) REFERENCES `files` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
