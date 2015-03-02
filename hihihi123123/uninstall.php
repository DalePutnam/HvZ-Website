<?php
require_once("../require/sql.php");

$sql->query("DROP TABLE `hvz_archive`");
$sql->query("DROP TABLE `hvz_player_archive`");
$sql->query("DROP TABLE `hvz_stun_archive`");
$sql->query("DROP TABLE `hvz_tag_archive`");
$sql->query("DROP TABLE `hvz_game_archive`");
$sql->query("DROP TABLE `hvz_score_copy`");
$sql->query("DROP TABLE `hvz_supply_codes`");
$sql->query("DROP TABLE `hvz_mail`");
$sql->query("DROP TABLE `hvz_game_info`");
$sql->query("DROP TABLE `hvz_oz_list`");
$sql->query("DROP TABLE `hvz_oz_pool`");
$sql->query("DROP TABLE `hvz_stuns`");
$sql->query("DROP TABLE `hvz_tags`");
$sql->query("DROP TABLE `hvz_players`");

$sql->close();

?>