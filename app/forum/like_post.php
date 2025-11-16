<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin();
$session = get_user_from_session();
$continue = $_GET['continue'] ?? WEB_ROOT . "/forum.php";

if (!isset($_GET['id'])) {
    header("location: $continue");
    die;
}

$id = intval($_GET['id']);
$existing = has_liked($id, $session['id']);

if ($existing) {
    unlike_post($id, $session['id']);
} else {
    like_post($id, $session['id']);
}

header("location: $continue");