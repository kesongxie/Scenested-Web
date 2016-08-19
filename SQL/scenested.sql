-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 19, 2016 at 03:03 AM
-- Server version: 5.5.42
-- PHP Version: 7.0.8

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
-- Table structure for table `device_token`
--

CREATE TABLE `device_token` (
  `device_token_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deviceToken` varchar(100) NOT NULL,
  `created_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `feature`
--

CREATE TABLE `feature` (
  `feature_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'the user who created the theme',
  `name` varchar(100) NOT NULL COMMENT 'the name of the theme',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `feature`
--

INSERT INTO `feature` (`feature_id`, `user_id`, `name`, `created_time`) VALUES
(135, 107, 'TENNIS', '2016-08-18 02:48:35'),
(136, 107, 'GUITAR', '2016-08-18 08:49:37'),
(141, 107, 'MOVIE', '2016-08-18 10:51:30'),
(142, 107, 'MUSIC', '2016-08-18 11:02:55');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `feature_id` int(11) NOT NULL,
  `created_time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `user_id`, `text`, `feature_id`, `created_time`) VALUES
(83, 107, 'Roger and Novak are about to show up. Here I''m at the #USOPEN 2015 final.', 135, '2016-08-18 05:17:22'),
(85, 107, 'Scene like a movie. And it acts like a song #snowing', 141, '2016-08-18 07:13:55');

-- --------------------------------------------------------

--
-- Table structure for table `post_like`
--

CREATE TABLE `post_like` (
  `post_like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `post_user_id` int(11) NOT NULL COMMENT 'who posted the post',
  `liked_user_id` int(11) NOT NULL COMMENT 'who liked the post',
  `like_time` datetime NOT NULL,
  `view` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post_like`
--

INSERT INTO `post_like` (`post_like_id`, `post_id`, `post_user_id`, `liked_user_id`, `like_time`, `view`) VALUES
(1, 83, 107, 33, '2016-08-17 12:12:19', '0');

-- --------------------------------------------------------

--
-- Table structure for table `post_photo`
--

CREATE TABLE `post_photo` (
  `post_photo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'who posted the photo',
  `post_id` int(11) NOT NULL COMMENT 'which scene this photo belongs to',
  `picture_url` varchar(255) NOT NULL,
  `aspect_ratio` float NOT NULL,
  `upload_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL COMMENT 'a unique hash to identify this photo'
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post_photo`
--

INSERT INTO `post_photo` (`post_photo_id`, `user_id`, `post_id`, `picture_url`, `aspect_ratio`, `upload_time`, `hash`) VALUES
(88, 107, 66, '9cd957639f29dad2a809332c/thumb_ec19753e895023f914c6426d.jpg', 1.33333, '2016-08-17 23:04:10', 'aeab95ccc07f0c5844aa534b'),
(105, 107, 83, '13106dba1faf865d1cd49548/thumb_3ef90ab7f4a87ee667d96735.jpg', 1.33333, '2016-08-18 05:17:24', '9f6634d693034afae5994128'),
(107, 107, 85, '0d49e8cb42393ed927eeca36/thumb_0375559f07a8fa58bb7f2326.jpg', 1.33333, '2016-08-18 07:13:57', '3c94dc879593104471e4b75e');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_time` datetime NOT NULL,
  `unique_iden` varchar(40) NOT NULL,
  `profileVisible` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0 if the profile is not visible, 1 otherwise'
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `fullname`, `password`, `created_time`, `unique_iden`, `profileVisible`) VALUES
(33, 'nicholastse', 'nicholas@yahoo.com', 'Nicholas Tse', '$2y$10$ejmW0zMvpnETsMB1M5sMDu1Gl.CRVbVgzO7sHY7CryBCWLnHvR5pK', '0000-00-00 00:00:00', '', '1'),
(34, 'kesongxie', 'kesong@yahoo.com', 'Bitch', '$2y$10$rYV.9e.g5i4/u7cDKoFY.eee2ajeeJPAXM5cnW/YH/EB/gKm6n65W', '0000-00-00 00:00:00', '', '0'),
(107, 'kesongtse', 'Kesongxie1993@gmail.com', 'Kesong Xie', '$2y$10$4UcUYxuuDdj/Fn/j4fHNQO46ZarTRMuhUtd4kqxm6Yxidx8waUTaK', '2016-08-08 21:55:30', '', '1'),
(109, 'uckesong', 'k8xie@ucsd.edu', 'Kesong Xie', '$2y$10$N3CgtCfgbbKKQpLukCylu.IZ41omm5aDHbRWvZuhTukOYHhC4KvAi', '2016-08-10 15:13:58', '', '1');

-- --------------------------------------------------------

--
-- Table structure for table `user_bio`
--

CREATE TABLE `user_bio` (
  `user_bio_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` varchar(120) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_bio`
--

INSERT INTO `user_bio` (`user_bio_id`, `user_id`, `bio`, `time`) VALUES
(3, 33, 'I love programming.', '2016-08-10 04:04:26'),
(4, 34, 'A programming nerd.', '2016-08-04 15:23:19'),
(5, 107, 'Programming nerd rocks on guitar', '2016-08-18 17:11:22'),
(6, 108, '', '2016-08-10 05:07:26'),
(7, 109, 'I love programming.', '2016-08-10 17:41:07');

-- --------------------------------------------------------

--
-- Table structure for table `User_Feature_Cover`
--

CREATE TABLE `User_Feature_Cover` (
  `user_feature_cover_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `User_Feature_Cover`
--

INSERT INTO `User_Feature_Cover` (`user_feature_cover_id`, `user_id`, `feature_id`, `picture_url`, `upload_time`, `hash`) VALUES
(134, 107, 135, 'b225700a3a51bab11d5091c3/thumb_60e21544ec0257cae910771b.jpg', '2016-08-18 04:44:49', '6b706d90b9b4010ea9df7962'),
(135, 107, 136, 'e4103b2cfdba5bdef84ad9ba/thumb_093266097701e6588e1c08b2.jpg', '2016-08-18 04:49:38', '38ebe025387c58abfdf2f3ce'),
(140, 107, 141, '28e3975497078e626063026d/thumb_79d233b8610486f8b8552ac7.jpg', '2016-08-18 06:51:30', 'a4922f23dab18a925b95247a'),
(141, 107, 142, '36c5a6d881720b8b16a7d875/thumb_1ef5c2eb9a31696d5f24c8b8.jpg', '2016-08-18 07:02:57', 'cf55a9faf042c3b96279b3d5');

-- --------------------------------------------------------

--
-- Table structure for table `user_media_prefix`
--

CREATE TABLE `user_media_prefix` (
  `user_media_prefix_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `prefix` varchar(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_media_prefix`
--

INSERT INTO `user_media_prefix` (`user_media_prefix_id`, `user_id`, `prefix`) VALUES
(1, 33, '9e6dd8c93a28bbbfecb6e2d8'),
(2, 34, '382b88da231c1719112a367e'),
(3, 85, '6874e44cda21af211fc7d02f'),
(4, 86, '019d1a626dc2a14b402854e4'),
(5, 87, 'd02448851f9519b9d3dd196c'),
(6, 88, 'd09e7574c490101e001f9f76'),
(7, 89, '6bd37a31a8b132b56df7c02a'),
(8, 90, 'f866b335f89324fbc8d35fd6'),
(9, 91, '72bc0816dc945cb948c61ee0'),
(10, 92, '0fdcdad0bdd1543501fa5482'),
(11, 93, '1d87b248ceff98a0779681eb'),
(12, 94, '63973ae7fea08e468695649e'),
(13, 95, '2ffa584565b02000590376fa'),
(14, 96, '8130772bd2e7f0eb01e264eb'),
(15, 97, 'af7b42dfa758a22c1c71adb3'),
(16, 98, 'd8627e18350ebbcbb355b3e1'),
(17, 99, '879dce3e5ced50b340ae56f3'),
(18, 100, 'b1108adbc379a2fc984d73d9'),
(19, 101, '22d195f16ce8b1a306d69e23'),
(20, 102, 'f228874715393882133933cd'),
(21, 103, '660b2b785c1322ac7f828578'),
(22, 104, '32a2fa2cb8213712ae3c59ed'),
(23, 105, 'fde67e419cacba76caf33548'),
(24, 106, 'a613b097a187ae5fb126e625'),
(25, 107, '7563a498264036382646ac5b'),
(26, 108, 'b4f33e7f86a515b27cd968d0'),
(27, 109, 'daf8309354786424949bd195'),
(28, 6, '2b3be068904bb73978c0dad7'),
(29, 1, '659ffcf0fcd85b2a1644046d');

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
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile_avator`
--

INSERT INTO `user_profile_avator` (`user_profile_avator_id`, `user_id`, `picture_url`, `upload_time`, `hash`) VALUES
(106, 85, '930fc1984c3e3bdf4a31959d/thumb_6e5918ccf082ff181666e393.jpg', '2016-08-08 01:13:39', 'aab1b3d26399520b8ccd5ec0'),
(107, 86, '8fe2c80510464b32ea418689/thumb_37bfc321067bb8d4d5c9ce64.jpg', '2016-08-08 03:33:23', '5143cb52d2b41bca4b902c6d'),
(108, 87, 'd2bc2906d4dee9a4984eeef8/thumb_14ae30dbb34fac669de73d61.jpg', '2016-08-08 04:07:19', '9d2482f99febf5a8b2756ae8'),
(109, 88, '31f5e0143c5d9ad5bca689fa/thumb_683c37cd031a77c96d3c1bdf.jpg', '2016-08-08 17:41:58', 'a4f709403943be99a3cffbe8'),
(110, 89, '810ff49ea35dcc1bbb973dd1/thumb_9bb12daa6a9da0523d6c72da.jpg', '2016-08-08 21:05:25', '70cc1e8dcb56abd00e7d0a2a'),
(111, 90, '3978c7eb4954ab2a45d0a0ff/thumb_3a58792e17e51d37160eec91.jpg', '2016-08-08 21:18:58', '52c5f46c1d67d52573b7edb5'),
(112, 91, '787abc9bf792c83461363130/thumb_59cbf76d8d0725f2fa4db124.jpg', '2016-08-08 21:19:32', '7b7ca2366a12bf935ede5995'),
(113, 92, 'cad23d5ca73faeeda8c5fdef/thumb_1472c6235526209cbbd6bf94.jpg', '2016-08-08 21:20:36', '85ff50799f14a848ba630e96'),
(114, 93, '50283ae692f33af4a3e31207/thumb_0e9ccf993ecbe186534694c4.jpg', '2016-08-08 21:31:22', '5311dd6ef1865580c2fc6796'),
(115, 94, '7df14524caadee59293960cb/thumb_2dfbf543bb3c637d218bfe4d.jpg', '2016-08-08 21:32:33', 'cf9e9ec3f18063c883afb7fd'),
(116, 95, '7e84dafd547b873d71693e1b/thumb_f0456d06505ff04d76de38ae.jpg', '2016-08-08 21:33:57', '047b763104e5fd2c909bd85b'),
(117, 96, '55cc075903d7edfcdce1fa94/thumb_578d628395ef382b4a675c3a.jpg', '2016-08-08 21:38:05', '8b4ef449b61d69a70a13aa04'),
(118, 97, '1468ee7c5007955108dbb977/thumb_7d1039af2ae09bc5af783e57.jpg', '2016-08-08 21:41:10', 'b8d8f70b7e0ceeb2a31a88fa'),
(119, 98, '949ad48906f0420b28d81086/thumb_cc4990b4081a61d6dace47c2.jpg', '2016-08-08 21:43:30', '15ce721a50af7102f12d45f0'),
(120, 99, '21339438a8658927146ec9c6/thumb_46ea561eb6d7b10a884b6a59.jpg', '2016-08-08 21:44:33', 'cbee6eeb4e3ccd1c359750a5'),
(121, 100, 'ac4b0f2587a26f3db5f74d51/thumb_3da16dcc2f99f1d0874f145d.jpg', '2016-08-08 21:45:30', '4c9716e7a2e019b13038f39b'),
(122, 101, '8fca513e3787150051ad1d82/thumb_508f19476c46049b18292b68.jpg', '2016-08-08 21:46:43', '3cb42a134e8fb56a1f44db0d'),
(123, 102, 'd8b0502edc42e8c65036d108/thumb_e1882f60031f95c77374dbf1.jpg', '2016-08-08 21:47:42', 'b112b793a59090c3ace5f73b'),
(124, 103, 'b5a791df88ff2525ebdce48b/thumb_9d5a0e55b07069d6d74333bf.jpg', '2016-08-08 21:49:40', 'e9814f74f0ab4780c8fb5632'),
(125, 104, '0e4895fa38270b8160de2859/thumb_7bc99dce3539a8b08ac983b1.jpg', '2016-08-08 21:51:56', 'fac1da95946fbfde8a97f384'),
(126, 105, 'af8f39d52b679de3aef3cdd8/thumb_e24f5e5208a5c8799b119ff5.jpg', '2016-08-08 21:52:53', 'c19fbcc3c10deec3aeed8544'),
(127, 106, '3e5ae70263082b4d15ef839d/thumb_3adaf42a059f029996c1b55e.jpg', '2016-08-08 21:53:47', '5a59b9b6f40d2113451f5f6e'),
(140, 33, '7c7b701949c304dcf7083782/thumb_8ab163aac6443b3eb39ec0af.jpg', '2016-08-10 04:04:26', '9a1c955dab49e715b366be12'),
(149, 108, 'b3930f3e305bde66404e883b/thumb_6a033fb84d4ee2399c1e90ca.jpg', '2016-08-10 05:07:26', 'a95ad0c3beedb32277086edc'),
(156, 109, '1643180ccd6665319e3bb7d1/thumb_59d4f768347c4358fa956adc.jpg', '2016-08-10 17:41:07', '8da69ec0eb468720d4c03caf'),
(225, 107, '4c93d9aedea9ab85f8c1cbb8/thumb_8c0b57a36d1b51a6b66ddd0a.jpg', '2016-08-18 17:11:22', '31315062cd4084631c98d0b3');

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
) ENGINE=InnoDB AUTO_INCREMENT=363 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile_cover`
--

INSERT INTO `user_profile_cover` (`user_profile_cover_id`, `user_id`, `picture_url`, `upload_time`, `hash`) VALUES
(147, 34, '313314e8995c6b9383a5234b/thumb_ca432bab40af72360e13d4a3.', '2016-08-04 15:23:19', '0b20a4b6687c5a8fe6387b95'),
(278, 33, '967b6977010d1d57f212061c/thumb_3acc5f30fd43428e13abe5af.jpg', '2016-08-10 04:04:26', 'd5fd4fbbd8d180275f773a9a'),
(286, 108, '61df590fb23cccbeac8d410c/thumb_485dd2ce1de855f94606d5ec.jpg', '2016-08-10 05:07:26', '53999ba6954a2e417a9c4fea'),
(292, 109, '227de031344fd945a76ff8f5/thumb_9262e9d1b77559f11ff14d67.jpg', '2016-08-10 17:41:06', 'eb1ab9a045f8e86acd198a6b'),
(334, 108, 'a9ff2fe876cd71acb2df765e/thumb_18fa5f671c67bdc31dbe0878.jpg', '2016-08-12 02:35:50', '868caaed5f5b3dcf350109bf'),
(362, 107, '1343748955b5c48e13ecdc68/thumb_4b7d7d88a755a3a1f46220ab.jpg', '2016-08-18 17:11:22', 'ef83f84b5be6d1b907b3a210');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `device_token`
--
ALTER TABLE `device_token`
  ADD PRIMARY KEY (`device_token_id`);

--
-- Indexes for table `feature`
--
ALTER TABLE `feature`
  ADD PRIMARY KEY (`feature_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `post_like`
--
ALTER TABLE `post_like`
  ADD PRIMARY KEY (`post_like_id`);

--
-- Indexes for table `post_photo`
--
ALTER TABLE `post_photo`
  ADD PRIMARY KEY (`post_photo_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_bio`
--
ALTER TABLE `user_bio`
  ADD PRIMARY KEY (`user_bio_id`);

--
-- Indexes for table `User_Feature_Cover`
--
ALTER TABLE `User_Feature_Cover`
  ADD PRIMARY KEY (`user_feature_cover_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `device_token`
--
ALTER TABLE `device_token`
  MODIFY `device_token_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `feature`
--
ALTER TABLE `feature`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=147;
--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=89;
--
-- AUTO_INCREMENT for table `post_like`
--
ALTER TABLE `post_like`
  MODIFY `post_like_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `post_photo`
--
ALTER TABLE `post_photo`
  MODIFY `post_photo_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=111;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=110;
--
-- AUTO_INCREMENT for table `user_bio`
--
ALTER TABLE `user_bio`
  MODIFY `user_bio_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `User_Feature_Cover`
--
ALTER TABLE `User_Feature_Cover`
  MODIFY `user_feature_cover_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=146;
--
-- AUTO_INCREMENT for table `user_media_prefix`
--
ALTER TABLE `user_media_prefix`
  MODIFY `user_media_prefix_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `user_profile_avator`
--
ALTER TABLE `user_profile_avator`
  MODIFY `user_profile_avator_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=226;
--
-- AUTO_INCREMENT for table `user_profile_cover`
--
ALTER TABLE `user_profile_cover`
  MODIFY `user_profile_cover_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=363;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
