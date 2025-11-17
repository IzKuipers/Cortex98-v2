<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin(true);
$session = get_user_from_session();

$continue = $_GET["continue"] ?? WEB_ROOT . "/forum/index.php";

if (!isset($_GET["id"])) {
    invalid_link($continue);
}

$id = $_GET["id"];
$existing = get_topic_by_id($id);

if (!$existing['success']) {
    error_message(
        "Failed to get topic",
        "The requested topic could not be retrieved for unlocking. " . $existing['message'],
        $continue
    );

    die;
}

$result = set_topic_lock($id, 0);

if (!$result['success']) {
    error_message(
        "Failed to update topic",
        "An error occurred while updating the topic. " . $result['message'],
        $continue
    );

    die;
}

header("location: " . WEB_ROOT . "/$continue");