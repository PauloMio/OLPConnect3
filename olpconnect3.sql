-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2025 at 03:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `olpconnect3`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `credit` decimal(10,2) DEFAULT NULL,
  `loggedin` datetime DEFAULT NULL,
  `loggedout` datetime DEFAULT NULL,
  `schoolid` varchar(50) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `status` enum('inactive','active') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `firstname`, `lastname`, `credit`, `loggedin`, `loggedout`, `schoolid`, `program`, `birthdate`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Zyril', 'Evangelista', 1500.50, '2025-08-22 10:46:10', '2025-08-22 12:46:10', '201080009', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', '2001-12-30', 'inactive', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(2, 'pj', 'em', 2500.50, '2025-08-22 10:46:10', '2025-08-22 12:46:10', '123', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', '2001-05-27', 'inactive', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(3, 'ben', 'ram', 1500.50, '2025-08-22 10:46:10', '2025-08-22 12:46:10', '201080005', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', '2001-10-10', 'inactive', '2025-08-22 04:46:10', '2025-08-22 04:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `account_ebook_favorite`
--

CREATE TABLE `account_ebook_favorite` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `ebook_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ebooks`
--

CREATE TABLE `ebooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `coverage` varchar(255) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `edition` varchar(50) DEFAULT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `copyrightyear` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ebook_category`
--

CREATE TABLE `ebook_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ebook_category`
--

INSERT INTO `ebook_category` (`id`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Generalities', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(2, 'Philosophy & Psychology', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(3, 'Religion', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(4, 'Social Sciences', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(5, 'Language', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(6, 'Natural Sciences', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(7, 'Applied Sciences', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(8, 'Arts', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(9, 'Literature & Rhetorics', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(10, 'History & Geography', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(11, 'Senior Highschool Books', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(12, 'Fiction', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(13, 'Graduate', '2025-08-22 04:46:10', '2025-08-22 04:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `ebook_location`
--

CREATE TABLE `ebook_location` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ebook_location`
--

INSERT INTO `ebook_location` (`id`, `location`, `created_at`, `updated_at`) VALUES
(1, 'Fiction', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(2, 'General Reference', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(3, 'Thesis', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(4, 'Filipiniana', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(5, 'Foreign', '2025-08-22 04:46:10', '2025-08-22 04:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `guestlog`
--

CREATE TABLE `guestlog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `school` varchar(255) DEFAULT NULL,
  `id_num` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_user`
--

CREATE TABLE `program_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_user`
--

INSERT INTO `program_user` (`id`, `program`, `created_at`, `updated_at`) VALUES
(1, 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(2, 'BACHELOR OF SCIENCE IN COMPUTER ENGINEERING', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(3, 'BACHELOR OF SCIENCE IN NURSING', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(4, 'BACHELOR OF SCIENCE IN CRIMINOLOGY', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(5, 'BACHELOR OF SCIENCE IN EDUCATION', '2025-08-22 04:46:10', '2025-08-22 04:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `research`
--

CREATE TABLE `research` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `accession_no` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_category`
--

CREATE TABLE `research_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_category`
--

INSERT INTO `research_category` (`id`, `category`, `created_at`, `updated_at`) VALUES
(1, 'EMPLOYEE RESEARCH OUTPUT', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(2, 'UNDERGRADUATE STUDENTS\' OUTPUT', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(3, 'MASTER\'S RESEARCH OUTPUT', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(4, 'DOCTORAL DISSERTION OUTPUT', '2025-08-22 04:46:10', '2025-08-22 04:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_department`
--

CREATE TABLE `tbl_department` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_department`
--

INSERT INTO `tbl_department` (`id`, `department`, `created_at`, `updated_at`) VALUES
(1, 'CITE', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(2, 'COA', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(3, 'CBA', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(4, 'COC', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(5, 'CNM', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(6, 'CHM', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(7, 'CTED', '2025-08-22 04:46:10', '2025-08-22 04:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('inactive','active') NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '2025-08-22 04:46:08', '$2y$12$QXCWFvDjlAmWuJsY8L8jkuwSRpFOZy8yOReBSswOPLohEcdwqJIPy', 'inactive', 'cZkUJo8UrX', '2025-08-22 04:46:09', '2025-08-22 04:46:09'),
(2, 'boyzmaker', 'zyril.evangelista@gmail.com', '2025-08-22 04:46:09', '$2y$12$92FW.cfiohiiKEjoTID2eeHFM.hy9Zv2.7Lvp8JHkVKms2w46dHPq', 'inactive', 'JWLXvSu3tb', '2025-08-22 04:46:09', '2025-08-22 04:46:09'),
(3, 'pjem', 'pjem@gmail.com', '2025-08-22 04:46:09', '$2y$12$hbAFEjLL/9Sz9Lp8fI0T7uqrE6rFcwphSpMPZjkPfPiVdQ7/woUp6', 'inactive', 'Ejmu8hqbpR', '2025-08-22 04:46:09', '2025-08-22 04:46:09'),
(4, 'Nik', 'defendingdemigod1975@gmail.com', '2025-08-22 04:46:09', '$2y$12$pAIKWToOKC9ZKlbL3fXBdus3C75S1Ugzh7iqD56siPXFejhrjMvvO', 'inactive', 'pMdPCs4xEP', '2025-08-22 04:46:10', '2025-08-22 04:46:10'),
(5, 'Olpcc College Library', 'olpcccollegelibrary@gmail.com', '2025-08-22 04:46:10', '$2y$12$UyhCHqMXybohuuhnqbHH6OD5m.6Q59xEkdZztrjlaNK8bZ2uTgNqK', 'inactive', 'WpxOIqyJEK', '2025-08-22 04:46:10', '2025-08-22 04:46:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_ebook_favorite`
--
ALTER TABLE `account_ebook_favorite`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_ebook_favorite_account_id_ebook_id_unique` (`account_id`,`ebook_id`),
  ADD KEY `account_ebook_favorite_ebook_id_foreign` (`ebook_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ebooks`
--
ALTER TABLE `ebooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ebook_category`
--
ALTER TABLE `ebook_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ebook_location`
--
ALTER TABLE `ebook_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guestlog`
--
ALTER TABLE `guestlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_user`
--
ALTER TABLE `program_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `research`
--
ALTER TABLE `research`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `research_category`
--
ALTER TABLE `research_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_department`
--
ALTER TABLE `tbl_department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `account_ebook_favorite`
--
ALTER TABLE `account_ebook_favorite`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ebooks`
--
ALTER TABLE `ebooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ebook_category`
--
ALTER TABLE `ebook_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ebook_location`
--
ALTER TABLE `ebook_location`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `guestlog`
--
ALTER TABLE `guestlog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_user`
--
ALTER TABLE `program_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `research`
--
ALTER TABLE `research`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `research_category`
--
ALTER TABLE `research_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_department`
--
ALTER TABLE `tbl_department`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_ebook_favorite`
--
ALTER TABLE `account_ebook_favorite`
  ADD CONSTRAINT `account_ebook_favorite_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `account_ebook_favorite_ebook_id_foreign` FOREIGN KEY (`ebook_id`) REFERENCES `ebooks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
