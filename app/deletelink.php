<?php

require_once(__DIR__ . "/lib/error.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/links.php");

$continue = "links.php";

if (isset($_GET["continue"]))
    $continue = $_GET["continue"];

verify_loggedin();

if (!isset($_GET["linkid"])) {
    invalid_link($continue);
}

$link_id = $_GET['linkid'];

confirm_message(
    "Delete link?",
    "Are you sure you want to delete this link? This cannot be undone.",
    $continue,
    "deletelinkconfirm.php?linkid=$link_id&continue=$continue",
    "Cancel",
    "Confirm"
);