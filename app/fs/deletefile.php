<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/fs.php");
require_once(__DIR__ . "/../lib/error.php");

verify_loggedin();
$session = get_user_from_session();

$continue = $_GET["continue"] ?? "files.php";

if (!isset($_GET["id"])) {
    invalid_link($continue);
}

$id = intval($_GET["id"]);
$existing = $fs->getFileInfo($id);

if (!$existing) {
    error_message(
        "Failed to delete item",
        "The specified item could not be found. Please try again.",
        $continue,
        "Try again"
    );

    die;
}

$type = $existing["type"];

if ($existing["owner"] != $session["id"] && !$session["admin"]) {
    error_message(
        "Permission denied",
        "You are not the owner of this " . $type . ". Only the owner can delete it.",
        $continue,
        "Okay"
    );

    die;
}

$error_msg = "Are you sure you want to delete this $type? This cannot be reverted.";

if ($existing["owner"] != $session["id"] && $session["admin"]) {
    $error_msg .= <<<HTML
    <br><br>
    <font color="#ff0000">
        <b>WARNING, ADMIN ACTION:</b>
    </font>
    <br>
    You DO NOT own this $type. You should only perform these kinds of actions if it's for moderation.
    HTML;
}


confirm_message("Delete " . $type . "?", $error_msg, $continue, "fs/deletefileconfirm.php?id=$id&continue=$continue", "Cancel", "Delete");
