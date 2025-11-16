<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin(true);
$session = get_user_from_session();

$continue = $_GET["continue"] ?? WEB_ROOT . "/forum/index.php";

if (!isset($_GET["id"])) {
    error_message("Invalid link", "Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue);
    die;
}

$id = $_GET["id"];

if (isset($_GET["confirm"])) {
    $result = delete_post($id);

    if (!$result['success']) {
        error_message("Failed to delete post", "The specified post could not be deleted. " . $result['message'], $continue);
        die;
    }

    header("location: $continue");
} else {
    confirm_message("Delete category?", "Are you sure you want to delete this post? This cannot be undone.", $continue, "forum/delete_post.php?id=$id&confirm=1", "Cancel", "Delete");
}
