<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");
require_once(__DIR__ . "/../lib/error.php");

verify_loggedin();
$session = get_user_from_session();
$continue = $_GET['continue'] ?? "forum/index.php";

if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_GET['topic_id'], $_POST['content'])) {
    invalid_link($continue);
}

$topic_id = intval($_GET["topic_id"]);
$reply_id = intval($_GET["reply_id"] ?? null);
$content = htmlspecialchars(htmlspecialchars($_POST['content']));

$result = create_post($topic_id, $session['id'], $content, $reply_id);

if (!$result['success']) {
    error_message(
        "Failed to create post",
        "The post could not be created. " . $result['message'],
        $continue,
        'Okay'
    );

    die;
}

header("location: " . WEB_ROOT . "/$continue");