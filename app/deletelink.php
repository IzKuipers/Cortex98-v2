<?php

require_once("lib/error.php");
require_once("lib/session.php");
require_once("lib/links.php");

$continue_url = "links.php";

if (isset($_GET["continue"]))
    $continue_url = $_GET["continue"];

verify_loggedin();

if (!isset($_GET["linkid"])) {
    error_message("Invalid link", "Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue_url);
    die;
}

$link_id = $_GET['linkid'];

confirm_message(
    "Delete link?",
    "Are you sure you want to delete this link? This cannot be undone.",
    $continue_url,
    "deletelinkconfirm.php?linkid=$link_id&continue=$continue_url",
    "Cancel",
    "Confirm"
);  