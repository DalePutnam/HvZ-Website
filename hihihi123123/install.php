<?php
error_reporting(E_ALL);
require_once("../require/sql.php");
require_once("install_config.php");

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_players` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `first_name` VARCHAR(255), `last_name` VARCHAR(255), `email` VARCHAR(255), `password_crypt` CHAR(32), `reg_date` DATETIME, `code` VARCHAR(255), `type` ENUM('NONE', 'HUMAN', 'ZOMBIE', 'ADMIN', 'BANNED', 'SPECTATE') NOT NULL, `score` INT DEFAULT 0, `show_score` INT DEFAULT 0, `waiver` INT DEFAULT 0, `subscribe` INT DEFAULT 1, INDEX(`code`(6)), INDEX(`type`))");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_tags` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `killer` INT, `victim` INT UNIQUE, `time` DATETIME, FOREIGN KEY (`killer`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE, FOREIGN KEY (`victim`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_stuns` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `killer` INT, `victim` INT, `helper`  INT NULL DEFAULT NULL, `time` DATETIME NOT NULL, `comment` TEXT NOT NULL, `ratified` DATETIME NULL, FOREIGN KEY (`killer`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE, FOREIGN KEY (`victim`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE, FOREIGN KEY (`helper`) REFERENCES `hvz_players`(`id`) ON DELETE SET NULL, INDEX(`time`), INDEX(`ratified`))");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_mail` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `to` VARCHAR(255), `subject` TEXT, `body` TEXT, `date` DATETIME)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_oz_pool` (`id` INT NOT NULL UNIQUE, FOREIGN KEY (`id`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_oz_list` (`id` INT NOT NULL UNIQUE, FOREIGN KEY (`id`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_oz_list` (`id` INT NOT NULL UNIQUE, FOREIGN KEY (`id`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_supply_codes` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `code` VARCHAR(255) NOT NULL UNIQUE, `player` INT, FOREIGN KEY (`player`) REFERENCES `hvz_players`(`id`) ON DELETE SET NULL, INDEX(`code`(4)))");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_game_info` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `game_started` INT, `start_date` DATETIME, `end_date` DATETIME, `reg_start` DATETIME, `reg_end` DATETIME, `maintenance` INT, `score_milestones` VARCHAR(255), `score_per_zed` VARCHAR(255), `allow_public_scores` INT, `score_per_supply` INT, `main_email` VARCHAR(255), `score_per_tag` INT)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_archive` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `created` datetime NOT NULL, PRIMARY KEY (`id`))");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_player_archive` (`real_id` int(11) NOT NULL AUTO_INCREMENT, `id` int(11) NOT NULL, `first_name` varchar(255) DEFAULT NULL, `last_name` varchar(255) DEFAULT NULL, `type` enum('NONE','HUMAN','ZOMBIE','ADMIN','BANNED','SPECTATE') DEFAULT NULL, `score` int(11) NOT NULL, `term` int(11) NOT NULL, `was_oz` int(11) NOT NULL, `reg_date` datetime NOT NULL, PRIMARY KEY (`real_id`), KEY `term` (`term`), KEY `id` (`id`), FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_tag_archive` (`real_id` int(11) NOT NULL AUTO_INCREMENT, `killer` int(11) DEFAULT NULL, `victim` int(11) NOT NULL, `time` datetime NOT NULL, `term` int(11) NOT NULL, PRIMARY KEY (`real_id`), KEY `killer` (`killer`), KEY `victim` (`victim`), KEY `term` (`term`), FOREIGN KEY (`killer`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE, FOREIGN KEY (`victim`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE, FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE )");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_stun_archive` ( `real_id` int(11) NOT NULL AUTO_INCREMENT, `killer` int(11) NOT NULL, `victim` int(11) NOT NULL, `helper` int(11) DEFAULT NULL, `time` datetime NOT NULL, `term` int(11) NOT NULL, PRIMARY KEY (`real_id`), KEY `killer` (`killer`), KEY `victim` (`victim`), KEY `helper` (`helper`), KEY `term` (`term`), FOREIGN KEY (`killer`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE, FOREIGN KEY (`victim`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE, FOREIGN KEY (`helper`) REFERENCES `hvz_player_archive` (`id`) ON DELETE CASCADE, FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE )");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_game_archive` (`real_id` int(11) NOT NULL,`start_date` datetime DEFAULT NULL,`end_date` datetime DEFAULT NULL,`reg_start` datetime DEFAULT NULL,`reg_end` datetime DEFAULT NULL,`score_milestones` varchar(255) DEFAULT NULL,`score_per_zed` varchar(255) DEFAULT NULL,`allow_public_scores` int(11) DEFAULT NULL,`score_per_supply` int(11) DEFAULT NULL,`main_email` varchar(255) DEFAULT NULL,`score_per_tag` int(11) DEFAULT NULL,`term` int(11) NOT NULL,PRIMARY KEY (`real_id`),KEY `term` (`term`), FOREIGN KEY (`term`) REFERENCES `hvz_archive` (`id`) ON DELETE CASCADE)");

if( !$result ) echo $sql->error;

$result = $sql->query("CREATE TABLE IF NOT EXISTS `hvz_score_copy` (`id` INT NOT NULL UNIQUE, `score` INT NOT NULL, FOREIGN KEY (`id`) REFERENCES `hvz_players`(`id`) ON DELETE CASCADE)");

if( !$result ) echo $sql->error;

$result = $sql->query("INSERT INTO `hvz_game_info` (`id`, `game_started`, `start_date`, `end_date`, `reg_start`, `reg_end`, `maintenance`, `score_milestones`, `score_per_zed`, `allow_public_scores`, `score_per_supply`, `main_email`, `score_per_tag`) VALUES (1, 0, '$start_date', '$end_date', '$reg_start', '$reg_end', 1, '$score_milestones', '$score_per_zombie', $allow_public_scores, $score_per_supply_code, '$main_email', '$score_per_tag');");

if( !$result ) echo $sql->error;

$result = $sql->query("INSERT INTO `hvz_players` (`id`, `first_name`, `last_name`, `email`, `password_crypt`, `reg_date`, `type`) VALUES (1, '$admin_first', '$admin_last', '$admin', MD5('$admin_password'), NOW(), 'ADMIN');");

if( !$result ) echo $sql->error;

$sql->close();

?>