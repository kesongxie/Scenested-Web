-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 26, 2016 at 04:44 AM
-- Server version: 5.5.42
-- PHP Version: 7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scenested`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_iden` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `gender` enum('0','1','2') NOT NULL DEFAULT '0',
  `ip` varchar(32) NOT NULL,
  `signup_date` datetime NOT NULL,
  `activated` enum('0','1') NOT NULL DEFAULT '0',
  `unique_iden` varchar(40) NOT NULL,
  `user_access_url` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_iden`, `password`, `firstname`, `lastname`, `gender`, `ip`, `signup_date`, `activated`, `unique_iden`, `user_access_url`) VALUES
(1, 'kesong.xie@stu.bmcc.cuny.edu', '$2y$10$s9fVbnb8UJGnU/bfqTie9ejeg1rLVuKMwYNGoeTV4skBRzcDNfIIi', 'Kesong', 'Xie', '2', '146.111.24.200', '2015-11-25 23:22:05', '1', '7128d358b44aca23062816cc', 'kesong.xie');

-- --------------------------------------------------------

--
-- Table structure for table `user_bio`
--

CREATE TABLE `user_bio` (
  `user_bio_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` varchar(120) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_bio`
--

INSERT INTO `user_bio` (`user_bio_id`, `user_id`, `bio`, `time`) VALUES
(3, 1, 'I design, and implement it.', '2016-01-24 00:17:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_media_prefix`
--

CREATE TABLE `user_media_prefix` (
  `user_media_prefix_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `prefix` varchar(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_media_prefix`
--

INSERT INTO `user_media_prefix` (`user_media_prefix_id`, `user_id`, `prefix`) VALUES
(1, 1, '9e6dd8c93a28bbbfecb6e2d8');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile_avator`
--

CREATE TABLE `user_profile_avator` (
  `user_profile_avator_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile_avator`
--

INSERT INTO `user_profile_avator` (`user_profile_avator_id`, `user_id`, `picture_url`, `upload_time`, `hash`) VALUES
(63, 1, '6e5219777d1a5aba159b83d6/thumb_19dc4da0a7326fba20e44314.jpg', '2016-01-14 19:27:43', '5db314c393b0f22c755b9f98');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile_cover`
--

CREATE TABLE `user_profile_cover` (
  `user_profile_cover_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile_cover`
--

INSERT INTO `user_profile_cover` (`user_profile_cover_id`, `user_id`, `picture_url`, `upload_time`, `hash`) VALUES
(96, 1, 'a5ed0af8931741dda4f34bde/thumb_dd79eabe3674fe1bf56c3f2c.jpg', '2016-01-20 21:10:34', 'd283cbb4a74298c9ce8d653a');

-- --------------------------------------------------------

--
-- Table structure for table `user_scene_label`
--

CREATE TABLE `user_scene_label` (
  `user_scene_label_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_scene_label`
--

INSERT INTO `user_scene_label` (`user_scene_label_id`, `user_id`, `name`) VALUES
(42, 1, 'Tennis'),
(43, 1, 'Programming'),
(44, 1, 'Guitar'),
(46, 1, 'Stanford');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_access_url` (`user_access_url`),
  ADD UNIQUE KEY `user_iden` (`user_iden`),
  ADD UNIQUE KEY `unique_iden` (`unique_iden`);

--
-- Indexes for table `user_bio`
--
ALTER TABLE `user_bio`
  ADD PRIMARY KEY (`user_bio_id`);

--
-- Indexes for table `user_media_prefix`
--
ALTER TABLE `user_media_prefix`
  ADD PRIMARY KEY (`user_media_prefix_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `prefix` (`prefix`);

--
-- Indexes for table `user_profile_avator`
--
ALTER TABLE `user_profile_avator`
  ADD PRIMARY KEY (`user_profile_avator_id`);

--
-- Indexes for table `user_profile_cover`
--
ALTER TABLE `user_profile_cover`
  ADD PRIMARY KEY (`user_profile_cover_id`);

--
-- Indexes for table `user_scene_label`
--
ALTER TABLE `user_scene_label`
  ADD PRIMARY KEY (`user_scene_label_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_bio`
--
ALTER TABLE `user_bio`
  MODIFY `user_bio_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_media_prefix`
--
ALTER TABLE `user_media_prefix`
  MODIFY `user_media_prefix_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_profile_avator`
--
ALTER TABLE `user_profile_avator`
  MODIFY `user_profile_avator_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `user_profile_cover`
--
ALTER TABLE `user_profile_cover`
  MODIFY `user_profile_cover_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT for table `user_scene_label`
--
ALTER TABLE `user_scene_label`
  MODIFY `user_scene_label_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=47;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
