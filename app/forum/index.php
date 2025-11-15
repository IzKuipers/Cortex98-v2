<?php
require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin();
$session = get_user_from_session();
$errors = [];

$categories = get_all_categories();

if (!$categories['success']) {
    $errors[] = $categories['message'];
}

$topics = get_all_topics();

if (!$topics['success']) {
    $errors[] = $topics['message'];
}

$posts = get_all_posts();

if (!$posts['success']) {
    $errors[] = $posts['message'];
}

var_dump ($topics);

if (count($errors) > 0) {
    error_message("Failed to load forums", implode(", ", $errors));
    die;
}

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
        <table width="700" border="0" cellpadding="0" cellspacing="2">
            <tr>
                <td width="500" valign="top" bgcolor="#ffcccc">
                    <div style="margin: 10px;">
                        <h1>Cortex 98 Forum</h1>
                        <p>Welcome to the Cortex 98 forum! From here you can talk to other Cortex 98 users about
                            whatever
                            your heart desires, from the comfort of your personal computer!</p>
                    </div>
                    <br>
                    <table border="0" cellpadding="2" cellspacing="2" bgcolor="#ffcccc" width="100%">
                        <tr bgcolor="#ff8888">
                            <td><b>Forum Categories</b></td>
                        </tr>
                        <tr>
                            <td>
                                <table border="0" cellspacing="6" width="100%">
                                    <?php foreach ($categories["items"] as $category): ?>
                                        <?php $topics = get_category_topics($category["id"]) ?>
                                        <tr>
                                            <td valign="top"><img src="../assets/books.gif" alt=""></td>
                                            <td>
                                                <h2>
                                                    <a href="view_category.php?id=<?= $category["id"] ?>">
                                                        <?= $category['name'] ?>
                                                    </a>
                                                </h2>
                                                <p><?= $category['description'] ?></p>
                                                <p style="margin-top: 5px; color: gray; margin-bottom: 10px;">
                                                    <?= count($topics['items']) ?> topics</p>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="200" valign="top">
                    <table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#eeeeff">
                        <tr bgcolor="#ccccff">
                            <td><b>Statistics</b></td>
                        </tr>
                        <tr>
                            <td>
                                <ul>
                                    <li><?= count($categories['items']) ?> categories</li>
                                    <li><?= count($topics['items']) ?> topics</li>
                                    <li><?= count($posts['items']) ?> posts</li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </center>
</body>

</html>