<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");
require_once(__DIR__ . "/../lib/error.php");

verify_loggedin();

$session = get_user_from_session();
$continue = $_GET["continue"] ?? "forum/index.php";

if (!isset($_GET["id"])) {
    invalid_link();
}

$id = $_GET["id"];

$existing = get_topic_by_id($id);

if (!$existing) {
    error_message(
        "Failed to delete topic",
        "An error occurred while trying to retrieve the topic. " . $existing['message'],
        $continue
    );

    die;
}

if ($existing['topic']['locked'] && !$session['admin']) {
    error_message(
        "Access denied",
        "The topic is locked. Only administrators can delete it at this time.",
        $continue
    );

    die;
}

if ($session['id'] !== $existing['topic']['owner'] && !$session['admin']) {
    error_message(
        "Access denied",
        "You don't own this topic. Only the owner of the topic or an administrator can delete it.",
        $continue
    );

    die;
}

if (isset($_GET["confirm"])) {
    $result = delete_topic($id);

    if (!$result['success']) {
        error_message(
            "Failed to delete topic",
            "The topic could not be deleted. " . $result['message'],
            $continue
        );

        die;
    }

    header("location: " . WEB_ROOT . "/forum/view_category.php?id=" . $existing['topic']['category_id']);
} else {
    confirm_message(
        "Delete topic?",
        "Are you sure you want to delete this topic? This will also delete all of its posts.",
        $continue,
        "forum/delete_topic.php?id=$id&continue=$continue&confirm=1",
        "Cancel",
        "Delete"
    );
}