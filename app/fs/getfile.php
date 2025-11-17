<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/fs.php");

verify_loggedin();

if (!isset($_GET["id"])) {
    invalid_link();
}

$id = $_GET["id"];
$fs->downloadFile($id);