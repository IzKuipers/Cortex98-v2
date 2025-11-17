<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin(true);
$session = get_user_from_session();

$continue = $_GET["continue"] ?? WEB_ROOT . "/admin/manage_categories.php";

if (!isset($_GET["id"])) {
    invalid_link($continue);
}

$id = $_GET["id"];

if (isset($_GET["confirm"])) {
    $result = delete_category($id);

    if (!$result['success']) {
        error_message(
            "Failed to delete category",
            "The specified category could not be deleted. " . $result['message'],
            $continue
        );

        die;
    }

    header("location: $continue");
} else {
    confirm_message("Delete category?", "Are you sure you want to delete this category? This will also delete the topics and posts that are part of it.", "admin/manage_categories.php", "admin/delete_category.php?id=$id&confirm=1", "Cancel", "Delete");
}
