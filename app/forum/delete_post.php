<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin(true);

$session = get_user_from_session();
$is_admin = $session['admin'] === 1;

$continue = $_GET["continue"] ?? WEB_ROOT . "/forum/index.php";

if (!isset($_GET["id"])) {
    invalid_link($continue);
}

$id = $_GET["id"];

$existing = get_post_by_id(intval($id));

if (!$existing['success']) {
    error_message(
        "Failed to get post",
        "An error occurred while retrieving the specified post. " . $existing['message'],
        $continue
    );

    die;
}

$topic = get_topic_by_id($existing['post']['topic']);

if (!$topic['success']) {
    error_message(
        "Failed to get topic",
        "An error occurred while retrieving the post's associated topic. " . $topic['message'],
        $continue
    );

    die;
}

$is_locked = $topic['topic']['locked'];

$can_delete = can_delete_post($existing, $topic, $session);

if (!$can_delete) {
    error_message(
        "Can't delete post",
        "You don't currently have permission to delete this post. Maybe the topic is locked or you're not the owner of the post.",
        $continue
    );

    die;
}

if (isset($_GET["confirm"])) {
    $result = delete_post($id);

    if (!$result['success']) {
        error_message(
            "Failed to delete post",
            "The specified post could not be deleted. " . $result['message'],
            $continue
        );

        die;
    }

    header("location: " . WEB_ROOT . "/$continue");
} else {
    confirm_message(
        "Delete post?",
        "Are you sure you want to delete this post? This cannot be undone.",
        $continue,
        "forum/delete_post.php?id=$id&confirm=1&continue=$continue",
        "Cancel",
        "Delete"
    );
}
