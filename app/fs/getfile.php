<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/fs.php");

verify_loggedin();

if (!isset($_GET["id"])) {
    error_message("Invalid link", "Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue);
    die;
}

$id = $_GET["id"];
$fs->downloadFile($id);