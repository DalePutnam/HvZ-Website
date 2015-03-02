<?php
/* Common configuration options */

// Directory for e-mail templates
$templates_dir = "templates/";

// File for rules
$rules_file = "documents/rules.pdf";
$map_file = "documents/map.png";

// Mailing list settings
$use_mailing_lists = false;
function use_mailing_lists() {
    global $use_mailing_lists;
    return $use_mailing_lists;
}

// Password for mailman
$mailing_list_password = "mailman_password";
$mailing_list_root = "https://mailman.mysite.ca/admin";

/* SQL Settings */

$sql_database = "hvz";
$sql_user = "hvz";
$sql_pass = "rosebud";
$sql_host = "localhost";
