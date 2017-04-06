-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2017 at 12:17 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE watsfic;

CREATE USER 'watsfic'@'localhost' IDENTIFIED BY 't60ncOV3FwspPKInlWcO';
GRANT ALL PRIVILEGES ON watsfic.* TO 'watsfic'@'localhost';

USE watsfic;

--
-- Database: `watsfic`
--

-- --------------------------------------------------------

--
-- Table structure for table `hvz_archive`
--

CREATE TABLE IF NOT EXISTS `hvz_archive` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_briefings`
--

CREATE TABLE IF NOT EXISTS `hvz_briefings` (
`id` int(11) NOT NULL,
  `release_time` datetime NOT NULL,
  `expire_time` datetime NOT NULL,
  `human_title` text CHARACTER SET utf8 NOT NULL,
  `human_body` text CHARACTER SET utf8 NOT NULL,
  `zombie_title` text CHARACTER SET utf8 NOT NULL,
  `zombie_body` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_game_archive`
--

CREATE TABLE IF NOT EXISTS `hvz_game_archive` (
`real_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `reg_start` datetime DEFAULT NULL,
  `reg_end` datetime DEFAULT NULL,
  `score_milestones` varchar(255) DEFAULT NULL,
  `score_per_zed` varchar(255) DEFAULT NULL,
  `allow_public_scores` int(11) DEFAULT NULL,
  `score_per_supply` int(11) DEFAULT NULL,
  `main_email` varchar(255) DEFAULT NULL,
  `score_per_tag` int(11) DEFAULT NULL,
  `term` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_game_info`
--

CREATE TABLE IF NOT EXISTS `hvz_game_info` (
`id` int(11) NOT NULL,
  `game_started` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `reg_start` datetime DEFAULT NULL,
  `reg_end` datetime DEFAULT NULL,
  `maintenance` int(11) DEFAULT NULL,
  `score_milestones` varchar(255) DEFAULT NULL,
  `score_per_zed` varchar(255) DEFAULT NULL,
  `allow_public_scores` int(11) DEFAULT NULL,
  `score_per_supply` int(11) DEFAULT NULL,
  `main_email` varchar(255) DEFAULT NULL,
  `score_per_tag` int(11) DEFAULT NULL,
  `inventory` text
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `hvz_game_info` (`id`, `game_started`, `start_date`, `end_date`, `reg_start`, `reg_end`, `maintenance`, `score_milestones`, `score_per_zed`, `allow_public_scores`, `score_per_supply`, `main_email`, `score_per_tag`, `inventory`) VALUES
(1, 1, '2015-07-08 00:00:00', '2015-07-14 23:59:00', '2015-07-01 00:00:00', '2015-07-07 23:59:00', 1, '5,25,50,100,250', '', 1, 5, 'uwhumansvszombies@gmail.com', 5, '');

-- --------------------------------------------------------

--
-- Table structure for table `hvz_mail`
--

CREATE TABLE IF NOT EXISTS `hvz_mail` (
`id` int(11) NOT NULL,
  `to` varchar(255) DEFAULT NULL,
  `subject` text,
  `body` text,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3084 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_oz_list`
--

CREATE TABLE IF NOT EXISTS `hvz_oz_list` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_oz_pool`
--

CREATE TABLE IF NOT EXISTS `hvz_oz_pool` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_players`
--

CREATE TABLE IF NOT EXISTS `hvz_players` (
`id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_crypt` char(32) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` enum('NONE','HUMAN','ZOMBIE','ADMIN','BANNED','SPECTATE') NOT NULL,
  `score` int(11) DEFAULT '0',
  `show_score` int(11) DEFAULT '0',
  `waiver` int(11) DEFAULT '0',
  `subscribe` int(11) DEFAULT '1',
  `bonus_score` int(11) DEFAULT '0',
  `location_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1306 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hvz_players`
--

INSERT INTO `hvz_players` (`id`, `first_name`, `last_name`, `email`, `password_crypt`, `reg_date`, `code`, `type`, `score`, `show_score`, `waiver`, `subscribe`, `bonus_score`, `location_id`) VALUES
(1000, 'Test', 'Admin', 'test@fakemail.com', 'e571541105a2b4a7295e6f293fb3ad36', '2016-07-09 14:18:50', 'N4Y69U', 'ADMIN', 0, 0, 0, 1, 0, 0);


-- --------------------------------------------------------

--
-- Table structure for table `hvz_player_archive`
--

CREATE TABLE IF NOT EXISTS `hvz_player_archive` (
`real_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `type` enum('NONE','HUMAN','ZOMBIE','ADMIN','BANNED','SPECTATE') DEFAULT NULL,
  `score` int(11) NOT NULL,
  `term` int(11) NOT NULL,
  `was_oz` int(11) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3441 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_signup_locations`
--

CREATE TABLE IF NOT EXISTS `hvz_signup_locations` (
`id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_stuns`
--

CREATE TABLE IF NOT EXISTS `hvz_stuns` (
`id` int(11) NOT NULL,
  `killer` int(11) DEFAULT NULL,
  `victim` int(11) DEFAULT NULL,
  `helper` int(11) DEFAULT NULL,
  `time` datetime NOT NULL,
  `comment` text NOT NULL,
  `ratified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_stun_archive`
--

CREATE TABLE IF NOT EXISTS `hvz_stun_archive` (
`real_id` int(11) NOT NULL,
  `killer` int(11) NOT NULL,
  `victim` int(11) NOT NULL,
  `helper` int(11) DEFAULT NULL,
  `time` datetime NOT NULL,
  `term` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2556 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_supply_codes`
--

CREATE TABLE IF NOT EXISTS `hvz_supply_codes` (
`id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `player` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_tags`
--

CREATE TABLE IF NOT EXISTS `hvz_tags` (
`id` int(11) NOT NULL,
  `killer` int(11) DEFAULT NULL,
  `victim` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_tag_archive`
--

CREATE TABLE IF NOT EXISTS `hvz_tag_archive` (
`real_id` int(11) NOT NULL,
  `killer` int(11) DEFAULT NULL,
  `victim` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `term` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3137 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `hvz_zombie_stun_scores`
--

CREATE TABLE IF NOT EXISTS `hvz_zombie_stun_scores` (
`id` int(11) NOT NULL,
  `score_per_zed` varchar(255) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hvz_archive`
--
ALTER TABLE `hvz_archive`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hvz_briefings`
--
ALTER TABLE `hvz_briefings`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hvz_game_archive`
--
ALTER TABLE `hvz_game_archive`
 ADD PRIMARY KEY (`real_id`), ADD KEY `term` (`term`);

--
-- Indexes for table `hvz_game_info`
--
ALTER TABLE `hvz_game_info`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hvz_mail`
--
ALTER TABLE `hvz_mail`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hvz_oz_list`
--
ALTER TABLE `hvz_oz_list`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `hvz_oz_pool`
--
ALTER TABLE `hvz_oz_pool`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `hvz_players`
--
ALTER TABLE `hvz_players`
 ADD PRIMARY KEY (`id`), ADD KEY `code` (`code`(6)), ADD KEY `type` (`type`);

--
-- Indexes for table `hvz_player_archive`
--
ALTER TABLE `hvz_player_archive`
 ADD PRIMARY KEY (`real_id`), ADD KEY `term` (`term`), ADD KEY `id` (`id`);

--
-- Indexes for table `hvz_signup_locations`
--
ALTER TABLE `hvz_signup_locations`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hvz_stuns`
--
ALTER TABLE `hvz_stuns`
 ADD PRIMARY KEY (`id`), ADD KEY `killer` (`killer`), ADD KEY `victim` (`victim`), ADD KEY `helper` (`helper`), ADD KEY `time` (`time`), ADD KEY `ratified` (`ratified`);

--
-- Indexes for table `hvz_stun_archive`
--
ALTER TABLE `hvz_stun_archive`
 ADD PRIMARY KEY (`real_id`), ADD KEY `killer` (`killer`), ADD KEY `victim` (`victim`), ADD KEY `helper` (`helper`), ADD KEY `term` (`term`);

--
-- Indexes for table `hvz_supply_codes`
--
ALTER TABLE `hvz_supply_codes`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `code` (`code`), ADD KEY `player` (`player`), ADD KEY `code_2` (`code`(4));

--
-- Indexes for table `hvz_tags`
--
ALTER TABLE `hvz_tags`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `victim` (`victim`), ADD KEY `killer` (`killer`);

--
-- Indexes for table `hvz_tag_archive`
--
ALTER TABLE `hvz_tag_archive`
 ADD PRIMARY KEY (`real_id`), ADD KEY `killer` (`killer`), ADD KEY `victim` (`victim`), ADD KEY `term` (`term`);

--
-- Indexes for table `hvz_zombie_stun_scores`
--
ALTER TABLE `hvz_zombie_stun_scores`
 ADD PRIMARY KEY (`id`), ADD KEY `date` (`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hvz_archive`
--
ALTER TABLE `hvz_archive`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `hvz_briefings`
--
ALTER TABLE `hvz_briefings`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `hvz_game_archive`
--
ALTER TABLE `hvz_game_archive`
MODIFY `real_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `hvz_game_info`
--
ALTER TABLE `hvz_game_info`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `hvz_mail`
--
ALTER TABLE `hvz_mail`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3084;
--
-- AUTO_INCREMENT for table `hvz_players`
--
ALTER TABLE `hvz_players`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1306;
--
-- AUTO_INCREMENT for table `hvz_player_archive`
--
ALTER TABLE `hvz_player_archive`
MODIFY `real_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3441;
--
-- AUTO_INCREMENT for table `hvz_signup_locations`
--
ALTER TABLE `hvz_signup_locations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `hvz_stuns`
--
ALTER TABLE `hvz_stuns`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `hvz_stun_archive`
--
ALTER TABLE `hvz_stun_archive`
MODIFY `real_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2556;
--
-- AUTO_INCREMENT for table `hvz_supply_codes`
--
ALTER TABLE `hvz_supply_codes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `hvz_tags`
--
ALTER TABLE `hvz_tags`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `hvz_tag_archive`
--
ALTER TABLE `hvz_tag_archive`
MODIFY `real_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3137;
--
-- AUTO_INCREMENT for table `hvz_zombie_stun_scores`
--
ALTER TABLE `hvz_zombie_stun_scores`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `hvz_game_archive`
--
ALTER TABLE `hvz_game_archive`
ADD CONSTRAINT `hvz_game_archive_ibfk_1` FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hvz_oz_list`
--
ALTER TABLE `hvz_oz_list`
ADD CONSTRAINT `hvz_oz_list_ibfk_1` FOREIGN KEY (`id`) REFERENCES `hvz_players` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hvz_oz_pool`
--
ALTER TABLE `hvz_oz_pool`
ADD CONSTRAINT `hvz_oz_pool_ibfk_1` FOREIGN KEY (`id`) REFERENCES `hvz_players` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hvz_player_archive`
--
ALTER TABLE `hvz_player_archive`
ADD CONSTRAINT `hvz_player_archive_ibfk_1` FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hvz_stuns`
--
ALTER TABLE `hvz_stuns`
ADD CONSTRAINT `hvz_stuns_ibfk_1` FOREIGN KEY (`killer`) REFERENCES `hvz_players` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_stuns_ibfk_2` FOREIGN KEY (`victim`) REFERENCES `hvz_players` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_stuns_ibfk_3` FOREIGN KEY (`helper`) REFERENCES `hvz_players` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hvz_stun_archive`
--
ALTER TABLE `hvz_stun_archive`
ADD CONSTRAINT `hvz_stun_archive_ibfk_1` FOREIGN KEY (`killer`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_stun_archive_ibfk_2` FOREIGN KEY (`victim`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_stun_archive_ibfk_3` FOREIGN KEY (`helper`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_stun_archive_ibfk_4` FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hvz_supply_codes`
--
ALTER TABLE `hvz_supply_codes`
ADD CONSTRAINT `hvz_supply_codes_ibfk_1` FOREIGN KEY (`player`) REFERENCES `hvz_players` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hvz_tags`
--
ALTER TABLE `hvz_tags`
ADD CONSTRAINT `hvz_tags_ibfk_1` FOREIGN KEY (`killer`) REFERENCES `hvz_players` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_tags_ibfk_2` FOREIGN KEY (`victim`) REFERENCES `hvz_players` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hvz_tag_archive`
--
ALTER TABLE `hvz_tag_archive`
ADD CONSTRAINT `hvz_tag_archive_ibfk_1` FOREIGN KEY (`killer`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_tag_archive_ibfk_2` FOREIGN KEY (`victim`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `hvz_tag_archive_ibfk_3` FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
