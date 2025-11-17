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

$pinned = get_pinned_topics();

if (!$pinned['success']) {
    $errors[] = $pinned['message'];
}

$posts = get_all_posts();

if (!$posts['success']) {
    $errors[] = $posts['message'];
}

if (count($errors) > 0) {
    error_message(
        "Failed to load forums",
        implode(", ", $errors)
    );

    die;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title>Forums - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <table width="700" border="0" cellpadding="0" cellspacing="2">
            <tr>
                <td width="500" valign="top" bgcolor="#fff6d4">
                    <div style="margin: 10px;">
                        <h1>Cortex 98 Forum</h1>
                        <p>Welcome to the Cortex 98 forum! From here you can talk to other Cortex 98 users about
                            whatever
                            your heart desires, from the comfort of your personal computer!</p>
                    </div>
                    <br>
                    <?php if (count($pinned['items'])): ?>
                        <table border="0" cellpadding="2" cellspacing="2" bgcolor="#fff6d4" width="100%">
                            <tr bgcolor="#ffe680">
                                <td><b>Pinned topics</b></td>
                            </tr>
                            <tr>
                                <td>
                                    <table border="0" cellspacing="6" width="100%">
                                        <?php foreach ($pinned['items'] as $topic): ?>
                                            <tr>
                                                <td valign="top"><img src="../assets/book.gif" alt=""></td>
                                                <td>
                                                    <h2>
                                                        <a href="view_topic.php?id=<?= $topic["id"] ?>">
                                                            <?= $topic['title'] ?>
                                                        </a>
                                                    </h2>
                                                    <p><?= str_replace("\n", "<br>", $topic['content']) ?></p>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>
                    <table border="0" cellpadding="2" cellspacing="2" bgcolor="#fff6d4" width="100%">
                        <tr bgcolor="#ffe680">
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
                                                    <?= count($topics['items']) ?> topics
                                                </p>
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
                                    <li><img src="../assets/symbols/books.gif" alt=""> <b><?= count($categories['items']) ?></b> categories</li>
                                    <li><img src="../assets/symbols/book.gif" alt=""> <b><?= count($topics['items']) ?></b> topics</li>
                                    <li><img src="../assets/symbols/pin.gif" alt=""> <b><?= count($pinned['items']) ?></b> pinned topics</li>
                                    <li><img src="../assets/symbols/file.gif" alt=""> <b><?= count($posts['items']) ?></b> posts</li>
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