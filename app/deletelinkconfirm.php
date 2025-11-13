<?php 

require_once(__DIR__ . "/lib/error.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/links.php");

$continue_url = "links.php";

if (isset($_GET["continue"])) $continue_url = $_GET["continue"];

verify_loggedin();

if (!isset($_GET["linkid"])) {
    error_message("Invalid link","Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue_url);
    die;
}

$result = delete_link_as_session($_GET["linkid"]);

if ($result !== true) {
    error_message("Delete failed","The link you wanted to delete could not be deleted. $result", $continue_url);
    die;
}

header("location: $continue_url");