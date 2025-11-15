<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin();
$session = get_user_from_session();

if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_POST['title'], $_POST['content'], $_POST['category'])) {
    header("Location: index.php");
    die;
}

$title = $_POST['title'];
$content = $_POST['content'];
$category_id = $_POST['category'];

$result = create_topic(intval($category_id), $session['id'], $title, $content);

if (!$result['success']) {
    error_message("Failed to create topic", "An error occurred while creating the topic. " . $result['message'], 'forum/index.php');
    die;
}

$topic_id = $result['id'];

header("Location: view_topic.php?id=$topic_id");