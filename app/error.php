<?php

require_once(__DIR__ . "/components/headerbar.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/error.php");

start_session_if_needed();

if (!isset($_SESSION["error_title"], $_SESSION["error_message"], $_SESSION["error_return"]))
    header("location: index.php");

$title = $_SESSION["error_title"];
$message = $_SESSION["error_message"];
$return = $_SESSION["error_return"];

$link = "Go back";

if (isset($_SESSION["error_link"]))
    $link = $_SESSION["error_link"];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/error.css">
    <title><?= $title ?> - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <div class="error-wrapper">
            <div class="error-body">
                <h1><?= $title ?></h1>
                <p><?= $message ?></p>
            </div>
            <div class="error-actions">
                <a href="<?= $return ?>" role="button"><?= $link ?></a>
            </div>
        </div>
    </center>
</body>

</html>