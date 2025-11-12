<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/fs.php");
require_once(__DIR__ . "/../lib/error.php");

verify_loggedin();
$session = get_user_from_session();

$continue = $_GET["continue"] ?? "files.php";

if (!isset($_GET["id"])) {
    error_message("Invalid link", "Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue);
    die;
}

$id = $_GET["id"];
$existing = $fs->getFileInfo($_GET["id"]);

if (!$existing) {
    error_message("Failed to delete item", "The specified item could not be found. Please try again.", $continue, "Try again");
    die;
}

if ($existing["owner"] != $session["id"] && !$session["admin"]) {
    error_message("Permission denied", "You are not the owner of this " . $existing["type"] . ". Only the owner can delete it.", $continue, "Okay");
    die;
}

if ($existing["type"] === "file") {
    $result = $fs->delete($id, $session["id"]);
} else {
    $result = $fs->deleteFolderRecursive($id, $session["id"]);
}

if (!$result["success"]) {
    error_message("Failed to delete " . $existing["type"], "The " . $existing["type"] . " could not be deleted. " . $result["message"],$continue);
    die;
}

header("location:$continue");