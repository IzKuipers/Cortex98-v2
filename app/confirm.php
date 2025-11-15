<?php

require_once(__DIR__ . "/components/navigation.php");
require_once(__DIR__ . "/components/headerbar.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/error.php");

start_session_if_needed();

if (
    !isset(
    $_SESSION["confirm_title"],
    $_SESSION["confirm_message"],
    $_SESSION["confirm_confirm_url"],
    $_SESSION["confirm_cancel_url"],
    $_SESSION["confirm_confirm_caption"],
    $_SESSION["confirm_confirm_caption"]
)
)
    header("location: index.php");

$title = $_SESSION["confirm_title"];
$message = $_SESSION["confirm_message"];
$confirm_url = $_SESSION["confirm_confirm_url"];
$cancel_url = $_SESSION["confirm_cancel_url"];
$confirm_caption = $_SESSION["confirm_confirm_caption"];
$cancel_caption = $_SESSION["confirm_cancel_caption"];

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
        <?= NavigationBar() ?>
        <div class="error-wrapper">
            <div class="error-body">
                <h1><?= $title ?></h1>
                <p><?= $message ?></p>
            </div>
            <div class="error-actions">
                <a href="<?= $cancel_url ?>"><?= $cancel_caption ?></a>
                <a href="<?= $confirm_url ?>"><?= $confirm_caption ?></a>
            </div>
        </div>
    </center>
</body>

</html>