<?php 

require_once("lib/error.php");
require_once("lib/session.php");
require_once("lib/links.php");

verify_loggedin();

if (!isset($_GET["linkid"])) {
    error_message("Invalid link","Sorry! This URL is not valid. You might've taken a wrong turn somewhere...");
    die;
}

click_link($_GET["linkid"]);