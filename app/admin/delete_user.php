<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/user.php");

verify_loggedin(true);
$session = get_user_from_session();

$continue = $_GET["continue"] ?? WEB_ROOT . "/admin/index.php";

if (!isset($_GET["id"])) {
    invalid_link($continue);
}

$id = $_GET["id"];

if (isset($_GET["confirm"])) {
    delete_user_by_id($id);

    header("location: $continue");
} else {
    confirm_message(
        "Delete user?",
        "Are you sure you want to delete this user? This will also delete everything that the user has added to the platform, along with any sub-items of those things.",
        "admin/index.php",
        "admin/delete_user.php?id=$id&confirm=1",
        "Cancel",
        "Delete"
    );
}
