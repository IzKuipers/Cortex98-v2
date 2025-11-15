<?php

require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin();
$session = get_user_from_session();

$id = $_GET["id"] ?? -1;

if ($id < 0) {
    header("location:index.php");
    die;
}

$category = get_category_by_id($id);

if (!$category['success']) {
    error_message("Failed to view category", "An error occurred while reading the specified category. " . $category['message'], "forum/index.php");
    die;
}

$topics = get_category_topics($id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title><?= $category['name'] ?> - Forum - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
    </center>
</body>

</html>