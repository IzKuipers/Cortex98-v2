<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/db.php");
require_once(__DIR__ . "/session.php");

function we_might_be_offline()
{
    $online = is_db_online();

    if (!$online)
        we_are_offline();
}

function we_are_offline()
{
    header("location: " . WEB_ROOT . "/offline.php");
    die;
}

function error_message(string $title, string $message, string $return_url = "index.php", string $link = "Go back")
{
    start_session_if_needed();

    $_SESSION["error_title"] = $title;
    $_SESSION["error_message"] = $message;
    $_SESSION["error_return"] = $return_url;
    $_SESSION["error_link"] = $link;

    header("location: " . WEB_ROOT . "/error.php");
}

function confirm_message(string $title, string $message, string $cancel_url, string $confirm_url, string $cancel_caption = "Cancel", string $confirm_caption = "Confirm")
{
    start_session_if_needed();

    $_SESSION["confirm_title"] = $title;
    $_SESSION["confirm_message"] = $message;
    $_SESSION["confirm_confirm_url"] = $confirm_url;
    $_SESSION["confirm_cancel_url"] = $cancel_url;
    $_SESSION["confirm_confirm_caption"] = $confirm_caption;
    $_SESSION["confirm_cancel_caption"] = $cancel_caption;

    header("location: " . WEB_ROOT . "/confirm.php");
}