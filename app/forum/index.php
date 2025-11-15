<?php
require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin();
$session = get_user_from_session();

$categories = get_all_categories();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title>Forum - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>

    </center>
</body>

</html>