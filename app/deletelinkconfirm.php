<?php

require_once(__DIR__ . "/lib/error.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/links.php");

$continue = "links.php";

if (isset($_GET["continue"]))
    $continue = $_GET["continue"];

verify_loggedin();

if (!isset($_GET["linkid"])) {
    invalid_link($continue);
}

$result = delete_link_as_session($_GET["linkid"]);

if ($result !== true) {
    error_message(
        "Delete failed",
        "The link you wanted to delete could not be deleted. $result",
        $continue
    );

    die;
}

header("location: $continue");