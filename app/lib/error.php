<?php

require_once(__DIR__ . "/../../config.php");
require_once("db.php");
require_once("session.php");

function we_might_be_offline() {
    $online = is_db_online();

    if (!$online) we_are_offline();
}

function we_are_offline() {
    header("location: " . WEB_ROOT . "/offline.php");
    die;
}

function error_message(string $title, string $message, string $return_url = "index.php", string $link = "") {
    start_session_if_needed();

    $_SESSION["error_title"] = $title;
    $_SESSION["error_message"] = $message;
    $_SESSION["error_return"] = $return_url;
    if ($link) $_SESSION["error_link"] = $link;

    header("location: " . WEB_ROOT . "/error.php");
}