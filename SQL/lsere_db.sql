-- phpMyAdmin SQL Dump
-- version 4.4.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Aug 13, 2015 at 07:50 PM
-- Server version: 5.5.42
-- PHP Version: 5.6.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lsere_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `auth_tokens`
--

INSERT INTO `auth_tokens` (`id`, `selector`, `token`, `user_id`, `expire`) VALUES
(57, '$2y$10$kvJGy4unsFzYIUEXlLDwWugrMf3f2f//znjN24M1uuKfQ4g5qWHTC', '70bff7426da45fbdb594a35268d464052a7f1d771154635f6822aca504bca171', 22, '2015-09-23 16:06:04'),
(58, '$2y$10$cBplXE0jqZ67DnCEi1zg..QYDMpqgCOcrqxBdzofPHn59xlJ3YD7G', 'da927047b7d0a75c5cc60300160ccb53636b673432d6583548be60b0f2bfc5bb', 22, '2015-09-23 19:31:46'),
(67, '$2y$10$M00Q/vZ4KjoeMgNYAnYhcunl14k3B/QI9QfEJ7oaEW89rO4SR5Wv.', '540ca4467a4b295e6a7a2244ce88278bf08e60708d999b684eb4756ae0cc3902', 22, '2015-09-26 18:00:23'),
(73, '$2y$10$q.SHlu./bDi5iiz1Sb1rruezqzo9r2yeQ/p9gK1wy02kWfcJMHwje', '33d7b8991aa3501393ab4a0f61d68e760d7efe62cbd7fe7480e385868054c0b2', 22, '2015-10-04 08:22:24'),
(103, '$2y$10$CwOKSxxkFzu7UlxaUYS6tOPE89J9D51x2AQ8reAnatKbcAvz487vq', '84c7ec89e165b51e3c992e66fd6ad4b98151a56b4199fbea795b70ad9e87defb', 22, '2015-10-12 10:49:03'),
(185, '$2y$10$uRBgY57NptE05K0COArHVeS/ILM8iQy24WH2ahZVE213Ie5IIZ8ea', '1913b6be19fda25843e4c802a9594e87a1497c9bf126880f49c4cb8bfc37fa0f', 33, '2015-11-09 15:15:42'),
(214, '$2y$10$bueiZRpoUBvf/2C/E/eE6elaE.TzKeOBHQ0HgcSjmojkuFOvn2Th2', '5ada5d3c59b9daaa6b96c19476029892d1f67fc5e35a9572d7db666f5a10c316', 22, '2015-11-11 15:34:45'),
(216, '$2y$10$QjwBSYTouFd7OmVJOofh7.Jm1xOhpo3Hx2PHjpQC6ebxl7EgSIJ1G', '534e20d752b5a0dae7bcce4a62901024b78008788467024bdaba08b9c6f504f3', 33, '2015-11-11 18:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL COMMENT 'comment on which activity',
  `user_id` int(11) NOT NULL COMMENT 'the user who sent the comment',
  `user_id_get` int(11) NOT NULL,
  `text` text NOT NULL,
  `sent_time` datetime NOT NULL,
  `user_view` enum('y','n') NOT NULL DEFAULT 'n',
  `view_time` datetime DEFAULT NULL,
  `hash` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `activity_id`, `user_id`, `user_id_get`, `text`, `sent_time`, `user_view`, `view_time`, `hash`) VALUES
(53, 56, 29, 22, 'today''s Roger''s performance is quite exhilarating !', '2015-07-10 19:06:32', 'n', NULL, '82b5f3879d2bbd1cd746bfc1'),
(59, 32, 29, 22, 'comentn here', '2015-07-10 19:22:39', 'n', NULL, '4d7d5898ff9b8fa8ff3b54a2'),
(60, 58, 28, 22, 'Although Roger lost in the final, the match he was against Murray is quite thrilling', '2015-07-16 00:08:04', 'n', NULL, 'be9ee0eae729bdb296df5880'),
(62, 58, 30, 22, 'How is your website going?', '2015-07-20 14:16:37', 'n', NULL, 'd6c7920fc34cb9762463e51f'),
(63, 58, 29, 22, 'Here is a greeting from there :)', '2015-07-22 18:44:54', 'n', NULL, 'ee3b783045ced02e14dc3020'),
(65, 67, 22, 28, 'how should I LAYOUT THE FRIENDS IN THE EVENT', '2015-07-26 14:37:39', 'n', NULL, 'e2b9d43694ea44b59d6d4d2c'),
(67, 46, 22, 22, 'The template and perfect integration would ultimately triumph', '2015-07-31 12:43:49', 'n', NULL, 'a7b844a9e10c6dc4b752ce97'),
(68, 67, 22, 28, 'twenty feeds per page load would be okay?', '2015-08-07 18:29:09', 'n', NULL, 'da29f9a9b2a59e8228d2ba2a'),
(69, 85, 29, 22, 'This is really nice', '2015-08-07 22:25:49', 'n', NULL, '2a57ea9aa5cf015f7360d5c1'),
(71, 46, 22, 22, 'Enjoy the beautiful design', '2015-08-11 00:48:01', 'n', NULL, '5640022b95835e05451a87fa'),
(73, 92, 22, 22, 'I would enjoy playing match with my friends, sometimes we play set against each other.', '2015-08-13 09:41:18', 'n', NULL, '9e6943f56995300555ca519f');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `user_id`, `school_id`, `major_id`, `hash`) VALUES
(2, 22, 1, 1, '6b492962add213d46dc73a28'),
(3, 29, 2, NULL, '2620767b2ae94752a8999355'),
(4, 31, 1, NULL, '6c73fd758ddfb9321fa14411'),
(5, 28, 1, NULL, '7b258f16078edf37f1f5d6f2'),
(16, 33, 1, 1, '5ffb4e57d184873910042720');

-- --------------------------------------------------------

--
-- Table structure for table `email_account_activation`
--

CREATE TABLE `email_account_activation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_account_activation`
--

INSERT INTO `email_account_activation` (`id`, `user_id`, `code`) VALUES
(1, 21, '6c11cec3815148d928211f6648a6dce9'),
(2, 22, 'c8706133019e16f5854d5376e6c7d5a5'),
(3, 23, 'f924906d1572e4a1a59752e112264664'),
(4, 24, '947df89d116a1ab75515e421089e0443'),
(5, 25, '883be88bb2eed5e62ec494ef362a86b8'),
(6, 26, '0f2e37598e5410c40c95e2bf1e988f53'),
(7, 27, '8ebf599ae75879f3183b78a496bbfbb6'),
(8, 28, '5916f3fd168ed8c777c550eb94d3c470'),
(9, 29, 'b71edef256d2aa635985853ef9bf4811'),
(10, 30, '59652db3717092b5e0ae7877b9efb0be'),
(11, 31, 'ddfb3a5281f3fcd5deb7e6ebdbd4a50f'),
(12, 32, '553d253053cc704238179f55ea90a235'),
(14, 33, '71fc70b8c608cc71745c9b725a10e9b4');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `interest_activity_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `location` text,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `interest_activity_id`, `title`, `description`, `location`, `date`, `time`) VALUES
(6, 46, 'US Open Tennis Match', 'The crowd in the US Open is amazing, I saw Roger Federer playing in the central court is unbelievable. Although he lost the match, but I''m luck to see him in the semi final ', 'USTA Billie Jean King National Tennis Center', '2014-09-06', '11:00:00'),
(9, 51, 'NSBE Lehman Hackathon 2015', 'My first Hackathon at City College and I coded straight for 8 hours. It was exhilarating and intensive. The experience is good but unfortunately, I was unable to finish what I want to build', 'City College New York (CUNY) NAC Ballroom', '2015-05-02', '09:00:00'),
(10, 54, 'Trace Bundy Acoustic Guitar Concert in NYC', 'I had an acoustic guitar concert in a nice venue with Trace Bundy. The crowd was amazing and the atmosphere was exhilarating. I wished I could have brought my guitar and had Trace signed for me :)', '85 Avenue A (between 5th & 6th) New York City, NY', '2014-05-17', '20:00:00'),
(11, 67, 'US Open 2015 in September', 'Anyone would like to come to see this year US Open, and I probably will go to see the final. Let me know anyone wants to come.', 'USTA Billie Jean King National Tennis Center', '2015-09-13', '12:00:00'),
(12, 92, 'Hangout to tennis court', 'I would like to find someone this saturday to hangout to tennis court', 'pier 40', '2015-08-09', '16:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `event_group`
--

CREATE TABLE `event_group` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event_group`
--

INSERT INTO `event_group` (`id`, `event_id`, `group_id`) VALUES
(1, 10, 1),
(15, 11, 15);

-- --------------------------------------------------------

--
-- Table structure for table `event_photo`
--

CREATE TABLE `event_photo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The user who uploaded the photo',
  `event_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `caption` text,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event_photo`
--

INSERT INTO `event_photo` (`id`, `user_id`, `event_id`, `picture_url`, `upload_time`, `caption`, `hash`) VALUES
(1, 22, 5, '32c42caedbce90f8716bd0df/thumb_99929806fab61d182f722901.jpg', '2015-03-10 11:36:11', 'night in madiaosn', '839a658c12237chbcca3eb66'),
(2, 22, 6, 'a7ef68a6f0286bfba7928bc6/thumb_d43090eb01b5145900886876.jpg', '2015-06-30 22:20:41', 'Crowd in US Open', '819a858c12237chbcca3eb66'),
(9, 22, 10, '66932afa697bdbc76a1a291e/thumb_d90ab4a531969620e2c2a160.jpg', '2015-07-05 11:00:34', NULL, '819a658c12237chbcca3eb66'),
(62, 22, 10, 'dac75fed049e72d512e6b576/thumb_62efcb95a081979e7069fdba.jpg', '2015-07-05 21:48:27', NULL, '819a658c12237c2bcca3eb66'),
(63, 22, 10, '0b897eb6025f817aaa2c030e/thumb_a60e931bfa86d1eb90f109ee.jpg', '2015-07-05 21:50:32', NULL, 'd4e484ba61935d1ec920c1a6'),
(64, 22, 10, '2f98ee4b3394a4a40b8620d8/thumb_1fc7a81a6978a821673aa0b4.jpg', '2015-07-05 21:51:04', NULL, '81aa93c0d1a86aa0662c01c7'),
(65, 22, 10, '99b3dcbb384db31cb7a18e93/thumb_a584d87c2904e32a7e4f13a7.jpg', '2015-07-05 21:53:32', NULL, 'b283c79a842989f04f4a7446'),
(68, 22, 9, '9f99fc5b2709e9ab88d03d5f/thumb_c3fd16d7997910155555cd16.jpg', '2015-07-05 22:01:59', NULL, 'ef171004009a3400aea21a06'),
(69, 22, 9, '53f0b2355523e617bd7cce40/thumb_c1b7025e519005cb0ebd663f.jpg', '2015-07-05 22:02:08', NULL, '07bb4f8b9f472af187033160'),
(71, 22, 9, '5f097da1108e82262f8552d3/thumb_fd801dec0c305eff7396df7c.jpg', '2015-07-05 22:02:47', NULL, 'bbf6f78fe522d7cfd31616f6'),
(74, 22, 6, '268866cceebfe7f20e5f27e6/thumb_17cb9a448c2ebae327c2ba29.jpg', '2015-07-05 22:22:30', NULL, 'f12c8c4b3e8fc6f011de2e73'),
(77, 22, 6, '4bd556bdcc284f1e4da05eac/thumb_0ce27094f51ba9183812e22f.jpg', '2015-07-05 22:23:25', NULL, '3ab884a24019b62809d58f8b'),
(78, 22, 6, '98c4e2903957201283575223/thumb_edb8deb65fd015382deecfc5.jpg', '2015-07-05 22:23:31', NULL, '08d2cbc310ce94b68dc37fbd'),
(79, 22, 6, 'cad244714f18b4f0835ca5aa/thumb_f15f7487449c9838a72e4fb5.jpeg', '2015-07-05 22:28:26', NULL, '5bfc0d969f425952bc194fc6'),
(80, 22, 6, '56e07684caad417ce49f9626/thumb_74b288d2fbb115e86a461cbe.jpeg', '2015-07-05 22:28:33', NULL, '02329cc08e34fc0cf4c2673d'),
(81, 22, 6, '93f13c4ba3be432f9736b782/thumb_5da0aff53f480759095e60db.jpeg', '2015-07-05 22:28:44', NULL, '7631e0a0b371086722caceeb'),
(82, 22, 9, '9e154fcf620b77a6a0f7a6be/thumb_b8d4e3a7b29930de49611309.jpg', '2015-07-07 06:45:10', NULL, '8ea3feadb29488660c377688'),
(83, 28, 11, '0a73b5fc9f3602791aebb63e/thumb_ed7befa2cc6af5116b77f09e.jpg', '2015-07-19 09:24:02', 'US Open', 'f72ab0c442e2daaae3e5f256'),
(84, 22, 12, 'd59031b7c7dd635d7278f9dc/thumb_5cacaaaf31ff905579d54117.jpg', '2015-08-09 10:53:25', '', '935ee9d2f781c45d0edcb2cf');

-- --------------------------------------------------------

--
-- Table structure for table `favor_event`
--

CREATE TABLE `favor_event` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `update_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `favor_event`
--

INSERT INTO `favor_event` (`id`, `user_id`, `title`, `description`, `update_time`, `hash`) VALUES
(25, 22, 'Programming Hackathon', 'let''s code something together', '2015-07-07 12:01:45', '296742d7a992fc5fddad7917'),
(26, 22, 'Guitar Meetup', 'I always want to find someone who plays guitar in the campus, it seems there is no Guitar Club in BMCC, that''s bad : (', '2015-07-07 12:02:34', '6d8c78c3d08ef925ac956a30'),
(27, 22, 'Tennis Hangout', 'I would like to find someone to hangout to the Tennis court during this lovely summer time', '2015-07-07 12:11:46', 'f07e6517ed8a4b54bf51f6a3');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `user_in` text NOT NULL,
  `created_time` datetime NOT NULL,
  `type` enum('e','r') NOT NULL DEFAULT 'r' COMMENT 'this group either a event group or a regular group',
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `user_in`, `created_time`, `type`, `hash`) VALUES
(1, '22,28,29,', '2015-07-21 00:00:00', 'e', '296742d7a992fc5fddad7917'),
(15, '22,28,29,', '2015-07-30 12:34:54', 'e', '11932bd7455b1b6fba7183c3');

-- --------------------------------------------------------

--
-- Table structure for table `group_message`
--

CREATE TABLE `group_message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'who sent the message',
  `group_id` int(11) NOT NULL COMMENT 'id either for event group or regular group id',
  `text` text NOT NULL,
  `sent_time` datetime NOT NULL,
  `message_type` enum('n','m') DEFAULT NULL COMMENT 'n stands for new member, m for event notification',
  `view_list` text NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `group_message`
--

INSERT INTO `group_message` (`id`, `user_id`, `group_id`, `text`, `sent_time`, `message_type`, `view_list`, `hash`) VALUES
(22, 28, 1, 'This is wonderfully lovely event', '2015-07-22 18:04:25', NULL, '22,28,29,', '4ab1faa267764a64861437c0'),
(23, 22, 1, 'I guess you are right', '2015-07-22 18:04:39', NULL, '28,22,29,', '6db8e4c536a3bf96fe7aa8d4'),
(24, 22, 1, 'No doubt that people from all round the world come to participate this event for 2 weeks', '2015-07-22 18:05:09', NULL, '22,29,28,', '30326370d32dcd07aaa9ceb0'),
(25, 29, 1, 'I want to make the world more open and connected', '2015-07-22 18:36:46', NULL, '22,28,29,', '787ed58b66662d21d07c39f0'),
(26, 28, 1, 'i would definitely bring my racket', '2015-07-23 09:55:49', NULL, '22,29,28,', '32de3ebd72b98ca280c5186d'),
(27, 28, 1, 'what do you think?', '2015-07-23 09:58:00', NULL, '22,29,28,', 'ee603bf24b4b81ab89eab1ce'),
(28, 22, 1, 'Finally finish the group chat functionality', '2015-07-23 18:53:36', NULL, '29,28,22,', 'ccf0b64e02547387927dcb74'),
(29, 29, 1, 'That''s super nice', '2015-07-23 18:54:02', NULL, '28,29,22,', '4e5dbf9481f823c23bc8cb91'),
(41, 22, 15, '', '2015-07-30 12:34:54', 'n', '28,22,29,', '13f6374dd89431c065f2e0c2'),
(42, 29, 15, '', '2015-07-30 12:35:44', 'n', '28,22,29,', '1f44e4cf7150a69f197b9bf4'),
(43, 28, 15, 'WONDERFUL', '2015-07-30 14:45:16', NULL, '22,28,29,', '9fc2409a04d7126e1ee8584b'),
(44, 22, 15, 'good to know', '2015-07-30 14:45:56', NULL, '28,22,29,', '2879cf8730c2e439a6043f7f'),
(45, 22, 1, 'i think we should change our ways of how we look at them', '2015-07-30 17:03:31', NULL, '29,22,28,', 'a8ce141ecd48e1dba372a839'),
(46, 29, 1, 'i really have no idea what''s going on with the group chat !', '2015-07-30 17:10:08', NULL, '22,28,29,', '0586242f560492bac845442b'),
(47, 22, 1, 'It seems the group chat starts to work', '2015-07-30 17:16:04', NULL, '22,28,29,', '201ac7fbddb0a1b322e33efc'),
(48, 22, 15, '', '2015-07-30 17:45:34', 'n', '22,28,29,', 'a93af825ef281d7f909f5640'),
(49, 22, 15, '', '2015-07-30 18:41:41', 'n', '22,28,29,', '37f48da534a0aa8efd66c500'),
(50, 22, 15, '', '2015-07-30 20:39:35', 'n', '28,22,29,', '1945f49a56ad63cb6ab03caa'),
(51, 22, 15, '', '2015-07-31 11:10:11', 'n', '28,22,29,', '7f45eaaa7cebe5aeedd7c9ad'),
(52, 22, 15, '', '2015-07-31 12:28:05', 'n', '28,22,29,', 'be7bd14c9923af05fcc598db'),
(53, 22, 15, '', '2015-07-31 12:29:52', 'n', '28,22,29,', '34ae420a057ac98e9d29032a'),
(54, 22, 15, '', '2015-07-31 12:29:59', 'n', '28,22,29,', '60c4a52101adf97f9b67c962'),
(55, 22, 15, '', '2015-07-31 12:30:05', 'n', '28,22,29,', 'ccb08c51961b76201d1d7c54'),
(56, 22, 15, '', '2015-07-31 12:30:09', 'n', '28,22,29,', '8f625bc9e9432e40f3b3f2fb'),
(57, 22, 15, '', '2015-07-31 12:30:12', 'n', '28,22,29,', '4045ff2dfcb01479f7341d41'),
(58, 22, 15, '', '2015-07-31 12:30:17', 'n', '28,22,29,', 'd74226551ecb3ec14b00a255'),
(59, 22, 15, '', '2015-07-31 12:30:20', 'n', '28,22,29,', 'ad70824845496d8d6edb99b3'),
(60, 22, 15, '', '2015-07-31 12:30:25', 'n', '28,22,29,', 'a10b038bb800011cffcb90d2'),
(61, 28, 15, 'it''s seems the member now is working?', '2015-07-31 17:51:39', NULL, '29,22,28,', '1ef4e1f9299f9e3ec83ca7ed'),
(62, 28, 1, 'This is awesome and the weather seems to be fascinating', '2015-08-07 09:23:47', NULL, '22,29,', 'a351c6f61e35ceb9a687c150'),
(63, 29, 1, 'that is awesome', '2015-08-07 23:27:53', NULL, '22,29,', '484e32e7d2629f8a132006fb'),
(64, 22, 15, 'It seems this event is so sweet', '2015-08-10 12:30:59', NULL, '29,28,22,', 'b3f73384590d4c56b68adcdc'),
(65, 29, 15, 'That''s correct!', '2015-08-10 12:31:46', NULL, '28,22,29,', 'dc2663707c3acaaf4052d29e'),
(66, 28, 15, 'i have been told by this', '2015-08-10 12:32:45', NULL, '22,29,28,', '61c110ade8b441ba57974f4c'),
(67, 22, 15, 'I will, definitely', '2015-08-10 12:34:09', NULL, '22,29,28,', 'c2a8df69d704f7a15cc137f5'),
(68, 28, 15, 'Yesterday Shane won over 6:0 6:0, I have never lost a match like that, but I will try harder. Failed again, try again. failed better', '2015-08-13 09:38:59', NULL, '', '4e1fdf6c18954364ba056015');

-- --------------------------------------------------------

--
-- Table structure for table `interest`
--

CREATE TABLE `interest` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `experience` int(2) NOT NULL,
  `create_time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `interest`
--

INSERT INTO `interest` (`id`, `user_id`, `name`, `description`, `experience`, `create_time`) VALUES
(55, 22, 'Tennis', 'I have been playing Tennis for 4 years. My favorite hangout location is Huston River park pier 40, just 15 mins walk from BMCC. I enjoy going to US Open and last year I saw my favorite Tennis player Roger Federer performed in the semi-final.', 4, '2015-06-20 19:43:24'),
(58, 22, 'Programming', 'I''m always occupied by coding. I''ve learnt web development for 2 years, and I started to build this website for fun. Now I decide to take this website a little bit more serious, and spent my time working on the aesthetic design', 2, '2015-06-22 10:57:18'),
(67, 22, 'Guitar', 'I have been playing guitar for 3 years and I almost practice guitar every nights since the first day I picked up the guitar. I prefer fingerstyle and enjoy going to acoustic guitar concerts and performance.One of my favorite guitarist is Trace Bundy, and I saw my first acoustic guitar concert last May by Trace Bundy in a nice Doom Venue	.', 4, '2015-06-24 07:41:36'),
(68, 29, 'Guitar', 'I have been playing Electronic Guitar for 5 years and perform professionally with my band. I''m crazy about music and rock style', 5, '2015-07-08 07:18:36'),
(69, 31, 'Jazz', 'Quentin has received numerous awards and scholarships since he began studying and performing. These have included The Helpmann Academy''s Keith Michell Award[5] in 2010, The first time a jazz musician had ever won the award. He has also won Downbeat Jazz Awards for ''Jazz Soloist'' (2012)[6] and ''Jazz Composition'' (2011 and 2012).[7] He has also been part of The Betty Carter Jazz Ahead residency at the Kennedy Center, Washington DC in 2011 and 2013. He plays guitar for more than 20 years', 11, '2015-07-14 10:23:45'),
(70, 30, 'Coding', 'I think why not build a website that allows me to find someone who also plays tennis in the campus. Without any knowledge about web development, I started to spend a lot of time on several new programming languages.', 2, '2015-07-15 13:36:00'),
(71, 28, 'Tennis', 'I enjoy going to US Open and Wimbledon. I have been playing tennis for 3 years by now and am able to play a decent rally.  My favorite player is Roger Federer', 3, '2015-07-15 23:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `interest_activity`
--

CREATE TABLE `interest_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL,
  `type` enum('m','e') NOT NULL,
  `post_time` datetime NOT NULL,
  `hash` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `interest_activity`
--

INSERT INTO `interest_activity` (`id`, `user_id`, `interest_id`, `type`, `post_time`, `hash`) VALUES
(32, 22, 55, 'm', '2015-06-26 13:40:55', '82bfef72d9986dfc9324d33b'),
(46, 22, 55, 'e', '2015-06-30 22:20:40', '0d3600196e72fb988a3a8d66'),
(47, 22, 67, 'm', '2015-06-30 22:30:44', '8f27c778e78d43909eef853b'),
(48, 22, 67, 'm', '2015-06-30 22:34:57', '86bd85b3b17e3be1e8a3da07'),
(51, 22, 58, 'e', '2015-07-01 10:45:03', 'b1d483dcb258e223fcb20b91'),
(52, 22, 67, 'm', '2015-07-01 22:23:13', '45e18d3a05618a7586bdd2af'),
(54, 22, 67, 'e', '2015-07-05 11:00:34', '155348794814e0ff61d2cf59'),
(56, 22, 55, 'm', '2015-07-10 13:50:18', '0e667dbaa5ebfedfdc2362ec'),
(58, 22, 55, 'm', '2015-07-12 22:03:47', '2a5e66feec0753e9d34166bc'),
(60, 30, 70, 'm', '2015-07-15 13:50:37', '61aa722cd0a019cf25ab49f3'),
(62, 28, 71, 'm', '2015-07-16 00:04:46', 'ea6a67f7f3f9b8760f6261e7'),
(67, 28, 71, 'e', '2015-07-19 09:24:02', '9fe5b8bbc6749f064758acb7'),
(85, 22, 55, 'm', '2015-08-05 19:27:53', 'ca10fdc0e1e239995806ac38'),
(88, 31, 69, 'm', '2015-08-07 13:53:39', 'efd7c108e643b6317be8ceff'),
(89, 29, 68, 'm', '2015-08-07 16:46:35', 'b302996b4d7638d5d2e08244'),
(92, 22, 55, 'e', '2015-08-09 10:53:25', 'd8fd5b96bfd1e81cd2cd9870'),
(93, 22, 55, 'm', '2015-08-13 11:15:18', '425a583d613d41c656ba0e0c');

-- --------------------------------------------------------

--
-- Table structure for table `interest_request`
--

CREATE TABLE `interest_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'the user who sent the request',
  `user_to` int(11) NOT NULL COMMENT 'user who receives the request',
  `interest_id` int(11) NOT NULL,
  `sent_time` datetime NOT NULL,
  `process` enum('i','y','n') NOT NULL DEFAULT 'n' COMMENT 'whether the user who receives the request have choose to accept or ignore or not, i is for ignore, y is for accept, n is for not process',
  `process_time` datetime DEFAULT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `interest_request`
--

INSERT INTO `interest_request` (`id`, `user_id`, `user_to`, `interest_id`, `sent_time`, `process`, `process_time`, `hash`) VALUES
(11, 22, 29, 58, '2015-07-12 17:53:52', 'y', '2015-07-12 17:54:05', 'e1b09f12d4e2c37ed203ae3a'),
(12, 22, 29, 67, '2015-07-14 09:20:22', 'y', '2015-07-14 09:20:37', '3e5ef2f040d7989f90a9589e'),
(13, 22, 31, 67, '2015-07-14 10:50:39', 'y', '2015-07-14 10:50:48', 'b26c6366476a7f23cf7438e2'),
(14, 31, 22, 69, '2015-07-14 10:59:21', 'y', '2015-07-14 17:00:39', 'c382bf45f49eac5357460079'),
(15, 31, 22, 69, '2015-07-14 17:13:29', 'y', '2015-07-14 17:13:34', 'f8a999c803fff4f6614a1906'),
(16, 31, 30, 69, '2015-07-14 17:19:26', 'y', '2015-07-14 17:19:40', '7f920fab6e681e2e47564e43'),
(17, 31, 22, 69, '2015-07-14 17:35:03', 'y', '2015-07-14 17:35:09', '19c1b44254abaae46b3963c1'),
(19, 30, 22, 70, '2015-07-15 16:41:11', 'y', '2015-07-15 16:47:50', '5b2a1538195338b2c7d088b0'),
(20, 22, 30, 58, '2015-07-15 16:48:12', 'y', '2015-07-15 21:53:16', '37b27de85a81a9099b6a8fd2'),
(21, 22, 28, 55, '2015-07-16 00:01:20', 'y', '2015-07-16 00:01:27', '80e0ff9552fe02f68e39dd80'),
(22, 22, 28, 67, '2015-07-17 23:44:23', 'n', NULL, '533e44d4a74a72611aac79f7'),
(23, 28, 22, 71, '2015-07-20 19:22:13', 'y', '2015-07-20 21:51:09', 'ce1239abd0e532c4a5d8d872'),
(24, 28, 22, 71, '2015-07-21 11:21:43', 'y', '2015-07-21 11:21:50', '33fe1ddf544f4627ff85cff2'),
(25, 28, 22, 71, '2015-07-21 11:23:47', 'y', '2015-07-21 11:23:52', '3aaa06a744fc0231eaf90542'),
(26, 29, 22, 68, '2015-07-23 12:07:43', 'y', '2015-07-23 12:07:58', '9e240f2150ff3d0588f78f35'),
(27, 22, 29, 67, '2015-07-23 12:40:38', 'y', '2015-07-23 12:40:42', '11a54d859f617d9bb970043e'),
(28, 22, 29, 55, '2015-08-10 23:29:46', 'i', '2015-08-10 23:30:44', '1c128e776a00b2af03a1c9b6'),
(29, 22, 33, 55, '2015-08-13 18:20:57', 'i', '2015-08-13 18:21:04', 'bc9e7dc5c824d13db560f2ea');

-- --------------------------------------------------------

--
-- Table structure for table `major`
--

CREATE TABLE `major` (
  `id` int(11) NOT NULL,
  `major_name` varchar(60) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `major`
--

INSERT INTO `major` (`id`, `major_name`) VALUES
(2, 'Cardiothoracic Surgery'),
(1, 'Computer Science');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_id_get` int(11) NOT NULL,
  `text` text NOT NULL,
  `sent_time` datetime NOT NULL,
  `view` enum('y','n') NOT NULL DEFAULT 'n',
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `user_id_get`, `text`, `sent_time`, `view`, `hash`) VALUES
(49, 30, 22, 'I want to note to you that i have successfully implement the individual chatting functionality', '2015-07-20 12:12:11', 'y', 'a666ff8f8df0c2aed2ea34fd'),
(50, 29, 22, 'no doubt you will go to this year''s US Open, right?', '2015-07-20 12:13:12', 'y', '708f62d78e2b3ce0aaac3424'),
(51, 22, 30, 'Quite impressive, but you still have a lot of hard work ahead', '2015-07-20 12:14:27', 'y', '102e68e96d1183f0af860143'),
(52, 30, 22, 'I will continue to work on that, be confident and determined', '2015-07-20 12:14:54', 'y', '8fcf0edc4923eaf22f1e6b6b'),
(53, 30, 22, 'I just working on the page title', '2015-07-20 14:47:33', 'y', 'ca4e15e5ce207800140c0c1c'),
(92, 22, 28, 'Hello, Nicholas', '2015-07-20 17:00:29', 'y', '2e4b3c24c07e3bfa16b7c1f8'),
(93, 28, 22, 'carry on, you may be able to triumph this time', '2015-07-20 17:01:22', 'y', '786ec7a6100cc6ea0f9477fe'),
(94, 29, 22, 'Let me try to see whether this works without message total?', '2015-07-20 17:04:33', 'y', 'f9269cf7071d3815d48244ab'),
(95, 22, 29, 'Now you can confirm that it works without the message total', '2015-07-20 17:05:16', 'y', '2e80f6ec9d9af70a7505f5b5'),
(96, 22, 29, 'Introducing the new Lsere, I would love to continue working on this!', '2015-07-20 18:02:09', 'y', 'b58006adc02efc85f1cc081c'),
(97, 22, 30, 'May i ask you what are u working on right now?', '2015-07-20 20:37:26', 'n', '6f01751f2644a0d914eb07e2'),
(98, 29, 22, 'Let''s see what it will turn out to be', '2015-07-21 12:22:22', 'y', '3b252b333197661a5d01eeb2'),
(99, 29, 22, 'I would like to whether you are able to make it this time', '2015-07-21 12:23:01', 'y', 'f044afa4fc7699799d539764'),
(100, 22, 30, 'I really have no idea how to approach this', '2015-07-28 09:59:34', 'n', '04f3bde8f601f5b7ca8d7b6e'),
(101, 29, 22, 'i believe i can make a dent in the universe', '2015-07-30 16:51:19', 'y', '0d9c2824755838b9572107da'),
(102, 22, 29, 'i believe so', '2015-07-30 16:51:56', 'y', '2e40f93552fdf709edb6c820'),
(103, 29, 22, 'what''s going on with the group chat?', '2015-07-30 17:09:37', 'y', 'fb68df1620b10ff763272789'),
(104, 29, 22, 'what would be the ideal time to do this?', '2015-07-30 17:10:33', 'y', '3e49923ddfc599e3ede0f6fe'),
(105, 22, 29, 'WHAT DO U MEAN?', '2015-07-30 17:15:16', 'y', '832bf55418bad6aa41f1d646'),
(106, 29, 22, 'i mean nothing', '2015-07-30 17:15:39', 'y', '7d3af771e4256d72eaae5098'),
(107, 22, 30, 'The journey of building great application is delicious', '2015-07-31 17:47:02', 'n', '071959c825b36d1bffd420c7'),
(108, 22, 29, 'I think tomorrow I am going to work on the search within the same campus', '2015-08-02 22:54:26', 'n', 'f3bca4e6f17c56b3d6305110'),
(109, 22, 28, 'I hope this site would become sucessful!', '2015-08-09 01:06:36', 'y', '30816dfe0c9f139f6e805027'),
(110, 28, 22, 'what would you do, if it doesn''t', '2015-08-10 12:34:32', 'y', '548d7743150d3e853561b1dc'),
(111, 22, 28, 'I don''t know, I just don''t want to give up so easily', '2015-08-13 17:56:02', 'n', 'ac1f7774c9af5d859d338c3f');

-- --------------------------------------------------------

--
-- Table structure for table `message_queue`
--

CREATE TABLE `message_queue` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `queue` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `message_queue`
--

INSERT INTO `message_queue` (`id`, `user_id`, `queue`) VALUES
(1, 22, 'g-15,m-28,m-29,g-1,m-30,'),
(2, 29, 'g-15,m-22,g-1,'),
(5, 30, 'm-22,'),
(6, 28, 'm-22,g-1,g-15,');

-- --------------------------------------------------------

--
-- Table structure for table `moment`
--

CREATE TABLE `moment` (
  `id` int(11) NOT NULL,
  `interest_activity_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `moment`
--

INSERT INTO `moment` (`id`, `interest_activity_id`, `description`, `date`) VALUES
(31, 32, 'I spent a night in the beautiful Huston River park tennis court. I have been always enjoyed playing tennis here. Nice sunset.', '2015-06-26'),
(32, 47, 'This is my first guitar I bought about 3 years ago when I have nothing knowledge about Guitar, it''s my friend Lao Xu who introduced me and make me fall in love the the guitar', '2011-12-23'),
(33, 48, 'My first acoustic guitar concert in the United States is of Trace Bundy''s in the amazing venue. He played my beloved Canon in D and show off his guitar skill on a I phone app with a song by metablica', '2014-05-17'),
(34, 52, 'Guitar is one of the most beautiful things i have ever met. I wish I could have started playing it when I was young.', '2015-07-01'),
(36, 56, 'I witnessed another Roger''s triumph on Wimbledon semi-final.  The moment is so familiar. Carry on Roger, and good luck on Sunday against Novak Djokovic', '2015-07-10'),
(38, 58, 'Today Roger lost his Wimbledon final to Novak, it was a great fight and he is about 34 years old and keep playing about 12 years now. I''m proud of him and he always plays in grace', '2015-07-12'),
(40, 60, 'Although I''m a programmer, I would be more appreciated if people call me designer. I gradually found out the elegant design is the drive for further implementation.', '2015-07-15'),
(42, 62, 'I enjoy going to US Open and Wimbledon. I have been playing tennis for 3 years by now and am able to play a decent rally. My favorite player is Roger Federer', '2015-07-24'),
(68, 85, 'It''s almost this year''s #USOpen, I hope this year Roger is able to get into the final and I will definitely try to see his match', '2015-08-05'),
(71, 88, 'Just tried to find some idea about improvising a song and it should be Jazz genre.', '2015-08-07'),
(72, 89, 'I have got to play in the scene for a long time, and will pick up my guitar again', '2015-08-07'),
(73, 93, 'When I bought my second racket, I have already decided to get back to Tennis.', '2015-08-13');

-- --------------------------------------------------------

--
-- Table structure for table `moment_photo`
--

CREATE TABLE `moment_photo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `moment_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `caption` text,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `moment_photo`
--

INSERT INTO `moment_photo` (`id`, `user_id`, `moment_id`, `picture_url`, `upload_time`, `caption`, `hash`) VALUES
(4, 22, 31, '5b12834101deb3b6c2a1fd8d/thumb_2d0723cd8c4f00a949d5a00c.jpg', '2015-06-26 13:40:55', 'Sunset in Huston River', 'f1a6649e723j8f3186f5f06a'),
(5, 22, 32, '6f864ccac487ad357782e136/thumb_11fb5e9b3ffaff0144d7abe4.jpg', '2015-06-30 22:30:44', 'My First Guitar', 'f1a66499723d8f3186f5f06a'),
(6, 22, 33, 'b31442cb137c6f5f7651931f/thumb_95580d1d1f0928545efd7e2e.jpg', '2015-06-30 22:34:58', 'Photo with Trace Bundy', 'e1a6649e723d8f3186f5f06a'),
(7, 22, 34, '9eeef552326582f41e237054/thumb_bff35ec4a0ca1619d2175a86.jpg', '2015-07-01 22:23:14', 'With my guitar', 'f1a66496723d8f3186f5f06a'),
(8, 22, 36, 'c3e3d9f7d99ebdf7154143bd/thumb_d5eac49fa8030b1cc23b2420.jpg', '2015-07-10 13:50:18', NULL, 'f1a6649e723d8f3186f5f06a'),
(9, 22, 38, '4d7d35ecaf72d38a01644658/thumb_6833b45b7c6cb0f2a73b4361.jpg', '2015-07-12 22:03:47', 'Roger in Wimbledon', 'dfd2815a4f7c089b0ed8102d'),
(10, 30, 40, 'b789f7674732626e635ccd90/thumb_78e5ee0e8134c9ad54c19017.png', '2015-07-15 13:50:37', 'Programming Joke', 'ac8f3264e89a7631413c9e89'),
(12, 28, 42, '0d6428f9bf502b37a8f45808/thumb_970ac04122205173bf74084c.jpg', '2015-07-16 00:04:46', 'Roger in Wimbledon', '56ca84f2f244ff54180b12cf'),
(13, 22, 68, 'e132c3df0a9309684b2a3ac4/thumb_afafb680ab12d5c063880de7.jpg', '2015-08-05 19:27:54', 'US Open 2015', '991c15f2364898b5a1ad09c9'),
(14, 31, 71, '97e14362173e839b968d4301/thumb_15aa794100f2636f7060bf1f.jpg', '2015-08-07 13:53:39', '', '0c50586be4fed572a178dec2'),
(15, 29, 72, '91c1b56b36aeadcd7a7be353/thumb_03b36a3f91504394c5a3685c.jpg', '2015-08-07 16:46:35', '', '099fcdf95baa9a6d1bdc9442'),
(16, 22, 73, '0bf7a45adad01bbf5db12304/thumb_07754f8fc1d6ababf38994b7.jpg', '2015-08-13 11:15:19', 'My second racket', 'd5021929b38ebbd6074d12f6');

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL COMMENT 'id for comment table ',
  `user_id` int(11) NOT NULL COMMENT 'user sent',
  `user_id_get` int(11) NOT NULL,
  `text` text NOT NULL,
  `sent_time` datetime NOT NULL,
  `user_view` enum('y','n') NOT NULL DEFAULT 'n',
  `view_time` datetime NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `hash` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reply`
--

INSERT INTO `reply` (`id`, `activity_id`, `comment_id`, `user_id`, `user_id_get`, `text`, `sent_time`, `user_view`, `view_time`, `target_id`, `hash`) VALUES
(11, 56, 53, 22, 29, 'Did you see it?', '2015-07-10 19:08:05', 'n', '0000-00-00 00:00:00', NULL, 'f28d0400d8c6b70ca8d409e1'),
(12, 56, 53, 29, 22, 'Yes, 3:0 against Murray, and the crowd was amazing too', '2015-07-10 19:08:51', 'n', '0000-00-00 00:00:00', 11, '7616469992ea38ecc06fab4d'),
(17, 32, 59, 22, 29, 'nice and good sound', '2015-07-10 19:22:50', 'n', '0000-00-00 00:00:00', NULL, '8c76105ad57000509a6aa108'),
(20, 56, 53, 22, 30, 'Let me post soemthing here to see what''s going on?', '2015-07-10 22:27:08', 'n', '0000-00-00 00:00:00', 19, 'd8b0adaab224da98af6e4a85'),
(22, 58, 62, 22, 30, 'Pretty well, thanks', '2015-07-20 14:17:21', 'n', '0000-00-00 00:00:00', NULL, '885f29c20552c970a5d5a8b1'),
(24, 67, 65, 28, 22, 'You have been working on the site for a while, what do you get?', '2015-07-30 21:12:34', 'n', '0000-00-00 00:00:00', NULL, 'b5c46d4e4e55f989a6bb4f7e'),
(25, 46, 67, 28, 22, 'This is a wonderful event', '2015-07-31 22:57:43', 'n', '0000-00-00 00:00:00', NULL, 'dffe6aab7782d5ef00885b84');

-- --------------------------------------------------------

--
-- Table structure for table `reply_notify_post_user`
--

CREATE TABLE `reply_notify_post_user` (
  `id` int(11) NOT NULL,
  `user_id_get` int(11) NOT NULL COMMENT 'post user',
  `reply_id` int(11) NOT NULL COMMENT 'id in the reply table',
  `hash` varchar(40) NOT NULL COMMENT 'the hash value is the same with the hash value in the corresponding row in the reply table'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `retrieve_account_code`
--

CREATE TABLE `retrieve_account_code` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `retrieve_account_code`
--

INSERT INTO `retrieve_account_code` (`id`, `user_id`, `code`) VALUES
(11, 22, '471315754a89fc19f17872fd');

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `id` int(11) NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `picture_url` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`id`, `school_name`, `picture_url`) VALUES
(1, 'Borough of Manhattan Community College', 'bmcc.png'),
(2, 'Stanford University', 'stanford.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_iden`, `password`, `firstname`, `lastname`, `gender`, `ip`, `signup_date`, `activated`, `unique_iden`, `user_access_url`) VALUES
(22, 'kesong_xie@yahoo.com', '$2y$10$5nLj3YekVCt0uP8f2XhMNOIFPrfv9i/tlSqqRAqU7xD6FeWF7x6dC', 'Kesong', 'Xie', '2', '::1', '2015-06-17 17:24:26', '1', 'c4a8afa33c5h2249106dd107', 'kesong.xie'),
(28, 'nicholas@yahoo.com', '$2y$10$3rzYr2n3kTwdorWM4v5LG.he71Lkx1d4gLUoKz8Xs9UvUXRyNxHGC', 'Nicholas', 'Tse', '2', '::1', '2015-06-17 18:05:02', '1', 'c4a8afa33c5f2249106dd1e7', 'kesong.xie.1'),
(29, 'meghan@yahoo.com', '$2y$10$3MF5GRZReQ5qrp1497Zwp.XlMs7xQA5ASYCxQNiblIXddGV1r0vBW', 'Meghan', 'Mccann', '1', '::1', '2015-06-17 20:30:13', '1', 'c4a8afa33c5f2249106dd107', 'meghan.mccann'),
(30, 'kenny@yahoo.com', '$2y$10$EY/UP8w7pVPh/9KaCTHOO.4rAA/jH2YXlKaixl5ohO6pbHD6g/leG', 'Kenny', 'Armenta', '2', '::1', '2015-06-27 12:22:27', '1', 'c4a8afaf3c5f2249106dd107', 'kenny.armenta'),
(31, 'quentinangus@gmail.com', '$2y$10$hm.TU2pADT5hnIvy1S/k6u1Ps0T85ggRb9G6vE3lZAt8PZW7JLXnm', 'Quentin', 'Angus', '2', '::1', '2015-07-14 10:13:10', '1', 'c4a8afa33c5f2249106dd10z', 'quentin.angus'),
(32, 'david@yahoo.com', '$2y$10$UFUoLlEzKROIR0ha/DFPZeBCLZrmdyK1vOfJpNf4DyYbz6XBUyFG2', 'David', 'Beckon', '2', '::1', '2015-07-17 13:17:46', '1', 'caa8afa33c5f2249106dd107', 'david.beckon'),
(33, 'shane@yahoo.com', '$2y$10$msMQtbluFgSDSR/b5nmbGeJx6pvydQPFXm4n7aPkYkP4R8VJsnX62', 'Shane', 'Rampersad', '2', '::1', '2015-08-11 16:05:33', '1', 'f1b9fb72da9b721de4236895', 'shane.rampersad');

-- --------------------------------------------------------

--
-- Table structure for table `user_interest_label_image`
--

CREATE TABLE `user_interest_label_image` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_interest_label_image`
--

INSERT INTO `user_interest_label_image` (`id`, `user_id`, `interest_id`, `picture_url`, `upload_time`, `hash`) VALUES
(27, 22, 67, '15d2474cad6dd5764d810578/thumb_489903e32fe4ad83b5c6b5c5.jpg', '2015-06-24 07:41:36', 'ece7047f1r102ef3a6f39bab'),
(35, 31, 69, '94aaa6f3a567d51f8f71f5ce/thumb_6226cfcb22e8efc570f1c43c.jpg', '2015-07-14 10:26:54', '748778577698872b8427ddc0'),
(36, 30, 70, 'bfbdbc5db9cb687760403239/thumb_065c5341145b6d5c461b2541.png', '2015-07-15 13:36:00', '6c280469d1ef933113a4572f'),
(37, 28, 71, '8bb6c0be055dfce0fa58ad2b/thumb_c5874aa8794bfcbf2a3090fe.jpg', '2015-07-15 23:57:26', '69e4257ef395656f29dcdeae'),
(38, 29, 68, '46831e698f03188806a4751e/thumb_1128cb72459ca15d12344841.jpeg', '2015-07-18 22:09:52', '1b16c8fbc2564cf68abdd8dd'),
(40, 22, 58, '8e7d76f8cbdca960ec12987a/thumb_f8c23cc82ee6c13fa3b23dad.png', '2015-08-07 21:45:24', '60dcce125d0efcc8f09139f0'),
(42, 22, 55, '358d30b1b67fe1460e4a222c/thumb_0ffde29ca8d0e29c51b6d19e.jpg', '2015-08-10 23:25:51', '2585b0db0a5bf7563a41946f');

-- --------------------------------------------------------

--
-- Table structure for table `user_in_interest`
--

CREATE TABLE `user_in_interest` (
  `id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'the user who created the interest',
  `user_in` int(11) NOT NULL,
  `in_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_in_interest`
--

INSERT INTO `user_in_interest` (`id`, `interest_id`, `user_id`, `user_in`, `in_time`, `hash`) VALUES
(6, 67, 22, 31, '2015-07-14 10:50:48', 'dc31e886cf843d742c01784c'),
(12, 70, 30, 22, '2015-07-15 16:47:50', '0b90717f377f234183b01595'),
(13, 58, 22, 30, '2015-07-15 21:53:16', '962e8513fd1fe901f4cc7abf'),
(14, 55, 22, 28, '2015-07-16 00:01:27', 'd5bd84657e85dfd9a7b118b2'),
(15, 68, 29, 22, '2015-07-23 12:07:58', '134b9a04439016230b6e292c'),
(16, 67, 22, 29, '2015-07-23 12:40:42', 'e9d9383624c240d032520d67');

-- --------------------------------------------------------

--
-- Table structure for table `user_media_prefix`
--

CREATE TABLE `user_media_prefix` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `prefix` varchar(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_media_prefix`
--

INSERT INTO `user_media_prefix` (`id`, `user_id`, `prefix`) VALUES
(13, 29, '0b6b58a7d212e078ed4662cb'),
(14, 22, '06f1e47fbdb03e48203ebfba'),
(15, 28, '597394f9fe626bbb1f13c37f'),
(16, 30, '299363e42aba1f6f3821b086'),
(17, 31, '48115f24e89820aede4825d6'),
(18, 32, '3dc386ee4329bd742330f543'),
(19, 33, 'b9b00c124c6a3e2d36bd8409');

-- --------------------------------------------------------

--
-- Table structure for table `user_notification_queue`
--

CREATE TABLE `user_notification_queue` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `queue` text NOT NULL,
  `read_queue` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_notification_queue`
--

INSERT INTO `user_notification_queue` (`id`, `user_id`, `queue`, `read_queue`) VALUES
(4, 22, '', 'c-69,ira-29,ira-28,r-25,r-24,c-64,ira-27,ir-26,c-63,ir-25,ir-24,ir-23,c-62,c-61,c-60,ira-21,ira-20,ir-19,'),
(5, 29, '', 'ir-28,ir-29,r-23,ir-27,ira-26,ir-12,ir-11,r-17,r-11,'),
(7, 30, '', 'r-22,r-21,ir-20,ira-19,ir-16,r-20,'),
(8, 31, '', 'ira-17,ira-16,ira-15,ira-14,ir-13,'),
(9, 28, '', 'c-68,ir-30,ir-28,c-65,ira-25,ira-24,ira-23,ir-22,'),
(10, 33, '', 'ir-29,');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile_cover`
--

CREATE TABLE `user_profile_cover` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile_cover`
--

INSERT INTO `user_profile_cover` (`id`, `user_id`, `picture_url`, `upload_time`, `hash`) VALUES
(16, 31, 'b19948daff64d42221cd6d86/thumb_69da6964c1c19607a6743a45.jpg', '2015-07-14 10:18:45', 'b00d3000ad791de90e71e851'),
(17, 31, '1d8f5e2d3d31e431c2bc730f/thumb_795c947e35c0a4e2f81729f5.jpg', '2015-07-14 10:20:02', 'e89004cec31ca4293b509920'),
(18, 31, '007385c5178d67bb17435f90/thumb_876013b7593d3f1b83a25110.jpg', '2015-07-14 10:25:45', 'bc07ceb8c33d95531d2560ae'),
(24, 30, '9420864a86b06079af3f75a8/thumb_6b7f7a966894ec2e448a0cd4.png', '2015-07-14 22:00:07', 'dd7e9a79998acad3db6e6ec9'),
(25, 28, '8211d78875aafbe92e59a98d/thumb_347d5436b433f27f3dbab721.jpg', '2015-07-15 23:55:35', 'a4a7a2559c418685a807d10c'),
(28, 32, 'd7f68bc7d9e110924a34d1ae/thumb_c66c4240542c458edebba021.jpg', '2015-07-17 13:33:35', '10d9d60a9a870e7c7c2cb3b2'),
(31, 29, 'd9d988615a51692803827e34/thumb_214ee8c418959f6781f226b2.jpg', '2015-07-17 14:46:38', '950b79ae7c023796834be0e9'),
(54, 22, 'ae2d4f3129c6bd267c877182/thumb_57e9b649fc3815bad8abc03a.jpg', '2015-08-08 23:51:01', '6a0bb8d3dc587017d1bfccd7'),
(59, 33, '447d834efe6b0ec89c8ebe3c/thumb_8e3071c4b39cfabc54b67325.jpg', '2015-08-13 18:35:35', 'ee1ecb0c757fe1d0d3413b2a');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile_picture`
--

CREATE TABLE `user_profile_picture` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `picture_url` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile_picture`
--

INSERT INTO `user_profile_picture` (`id`, `user_id`, `picture_url`, `upload_time`, `hash`) VALUES
(29, 31, '62beeaaf78b63bd5251f738c/thumb_9e4161990d500ebefa704ecf.jpg', '2015-07-14 10:18:04', 'b5d09aa563c27a62f6e3e83c'),
(30, 31, '513948663ee03d35fe1462a2/thumb_d92f1ad33b0c8546d245da7e.jpg', '2015-07-14 10:20:08', 'a61f072431fb54147a642bdb'),
(31, 31, '85a0677f030ad66c82051844/thumb_479ba2f4776372d8c050f198.jpg', '2015-07-14 10:21:27', '85a506ecd513765bb0be76b3'),
(32, 31, '42d5fe460ac4a3087e9abe6f/thumb_837e4fda655f88774bdb0a5d.jpg', '2015-07-14 10:25:49', 'dfb4d40ee498a162657b999b'),
(33, 31, '10059e85d3306094df94bb21/thumb_c403cf558ee807a776c20963.jpg', '2015-07-14 10:25:53', '8664b9a1f6d6a6269b8b734c'),
(35, 28, '4358d1f8e26a23e87ad92964/thumb_6e90cee991f25c6c904eabbb.jpg', '2015-07-15 23:56:07', '2780d3fd067a1b2ada0057f1'),
(36, 29, '406867654e08b646857e431d/thumb_be08ebe9354ca99c168b0976.jpg', '2015-07-16 10:29:14', '997043ef5b026476616d413a'),
(37, 30, '2a9d35115ad5c08ec9d5c8fd/thumb_02c48d8cb4df8d3bed75ea94.jpg', '2015-07-16 10:29:34', 'c277682918c76b4014183242'),
(40, 32, 'bc0174b02fe5735dfb780057/thumb_1b38cdf53f2033c68d682c51.jpg', '2015-07-17 13:31:49', '0c65acb473ccbc2aeb73dcac'),
(41, 28, '0d6f90406fc0cec43ad047ed/thumb_3409a8d2bf0f0a7a681d5071.jpg', '2015-07-30 18:57:11', 'a9bf28fe54e5c403053dccd7'),
(43, 22, '0e915e6f5ba440b4b4d10331/thumb_60c197fab169b72d69e22c55.jpg', '2015-08-05 21:37:47', '024ed76b4da3bf129303a0f2'),
(44, 33, '55317761875fe07bb5141442/thumb_aab8c57ff7774fd6ac48c1be.jpg', '2015-08-13 18:29:57', 'a4c4b22c9f87553ff5bed29d');

-- --------------------------------------------------------

--
-- Table structure for table `user_upcoming_event`
--

CREATE TABLE `user_upcoming_event` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_upcoming_event`
--

INSERT INTO `user_upcoming_event` (`id`, `user_id`, `event_id`, `hash`) VALUES
(1, 22, 11, '0'),
(2, 29, 11, '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_account_activation`
--
ALTER TABLE `email_account_activation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_group`
--
ALTER TABLE `event_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_photo`
--
ALTER TABLE `event_photo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favor_event`
--
ALTER TABLE `favor_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_message`
--
ALTER TABLE `group_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interest`
--
ALTER TABLE `interest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interest_activity`
--
ALTER TABLE `interest_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interest_request`
--
ALTER TABLE `interest_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `major`
--
ALTER TABLE `major`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `major_name` (`major_name`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_queue`
--
ALTER TABLE `message_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `moment`
--
ALTER TABLE `moment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `moment_photo`
--
ALTER TABLE `moment_photo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reply_notify_post_user`
--
ALTER TABLE `reply_notify_post_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `retrieve_account_code`
--
ALTER TABLE `retrieve_account_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_name` (`school_name`),
  ADD UNIQUE KEY `picture_url` (`picture_url`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_access_url` (`user_access_url`),
  ADD UNIQUE KEY `user_iden` (`user_iden`),
  ADD UNIQUE KEY `unique_iden` (`unique_iden`);

--
-- Indexes for table `user_interest_label_image`
--
ALTER TABLE `user_interest_label_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_in_interest`
--
ALTER TABLE `user_in_interest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_media_prefix`
--
ALTER TABLE `user_media_prefix`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `prefix` (`prefix`);

--
-- Indexes for table `user_notification_queue`
--
ALTER TABLE `user_notification_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profile_cover`
--
ALTER TABLE `user_profile_cover`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profile_picture`
--
ALTER TABLE `user_profile_picture`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_upcoming_event`
--
ALTER TABLE `user_upcoming_event`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=217;
--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=75;
--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `email_account_activation`
--
ALTER TABLE `email_account_activation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `event_group`
--
ALTER TABLE `event_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `event_photo`
--
ALTER TABLE `event_photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=85;
--
-- AUTO_INCREMENT for table `favor_event`
--
ALTER TABLE `favor_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `group_message`
--
ALTER TABLE `group_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=69;
--
-- AUTO_INCREMENT for table `interest`
--
ALTER TABLE `interest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=74;
--
-- AUTO_INCREMENT for table `interest_activity`
--
ALTER TABLE `interest_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=94;
--
-- AUTO_INCREMENT for table `interest_request`
--
ALTER TABLE `interest_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `major`
--
ALTER TABLE `major`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=112;
--
-- AUTO_INCREMENT for table `message_queue`
--
ALTER TABLE `message_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `moment`
--
ALTER TABLE `moment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=74;
--
-- AUTO_INCREMENT for table `moment_photo`
--
ALTER TABLE `moment_photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `reply_notify_post_user`
--
ALTER TABLE `reply_notify_post_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `retrieve_account_code`
--
ALTER TABLE `retrieve_account_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `user_interest_label_image`
--
ALTER TABLE `user_interest_label_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `user_in_interest`
--
ALTER TABLE `user_in_interest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `user_media_prefix`
--
ALTER TABLE `user_media_prefix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `user_notification_queue`
--
ALTER TABLE `user_notification_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `user_profile_cover`
--
ALTER TABLE `user_profile_cover`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT for table `user_profile_picture`
--
ALTER TABLE `user_profile_picture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `user_upcoming_event`
--
ALTER TABLE `user_upcoming_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
