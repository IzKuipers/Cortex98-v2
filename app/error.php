<?php

require_once("components/headerbar.php");
require_once("lib/session.php");
require_once("lib/error.php");

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/error.css">
    <title>Document</title>
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