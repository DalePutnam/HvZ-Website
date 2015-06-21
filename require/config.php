<?php
/* Common configuration options */

// Directory for e-mail templates
$templates_dir = "templates/";

// Directory for logs
$logs_dir = "log/";

// File for rules
$rules_file = "documents/rules.pdf";
$map_file = "documents/map.png";

// Mailing list settings
$use_mailing_lists = true;
function use_mailing_lists() {
    global $use_mailing_lists;
    return $use_mailing_lists;
}

$mailing_list_password = "letthegamebegin";
$mailing_list_root = "https://mailman.csclub.uwaterloo.ca/admin";

/* SQL Settings */

$sql_database = "watsfic";
$sql_user = "watsfic";
$sql_pass = "t60ncOV3FwspPKInlWcO";
$sql_host = "localhost";
