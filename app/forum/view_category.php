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
$last_activity = get_category_last_activity($id)

    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title><?= $category['name'] ?> - Forums - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <table border="0" width="700" cellpadding="2" cellspacing="2">
            <tr>
                <td width="450" valign="top">
                    <table border="0" width="100%" cellpadding="2" cellspacing="2" bgcolor="#fff6d4">
                        <tr bgcolor="#ffe680">
                            <td>
                                <a href="index.php">Forum</a> /
                                <img src="../assets/symbols/books.gif" alt=""> <?= $category['name'] ?> /
                                <img src="../assets/symbols/book.gif" alt=""> Topics
                            </td>
                            <td align="right" nowrap>
                                <a href="new_topic.php?id=<?= $id ?>">New topic</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" border="0" cellpadding="2" cellspacing="6">
                                    <?php foreach ($topics['items'] as $topic): ?>
                                        <?php
                                        $posts = get_topic_posts($topic['id']);
                                        $topic_stat = get_topic_last_activity($topic['id']);

                                        ?>
                                        <tr>
                                            <td valign="top">
                                                <img src="../assets/book.gif" alt="">
                                            </td>
                                            <td>
                                                <h2>
                                                    <a href="view_topic.php?id=<?= $topic["id"] ?>">
                                                        <?= $topic['title'] ?>
                                                    </a>
                                                </h2>
                                                <p style="white-space: pre;">
                                                    <?= str_replace("\n", "<br>", $topic['content']) ?></p>
                                                <p style="margin-top: 5px; color: gray; margin-bottom: 10px;">
                                                    <?= count($posts['items']) ?> posts - by
                                                    <?= $topic['owner'] === $session['id'] ? "you! " : $topic['username'] ?>
                                                    - last activity: <?= $topic_stat['last_activity'] ?>
                                                </p>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="250" valign="top">
                    <table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#fff6d4">
                        <tr bgcolor="#ffe680">
                            <td align="right">
                                <h2 style="margin: 5px;"><?= $category['name'] ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><?= $category['description'] ?></p>
                                <ul>
                                    <li>
                                        <b><?= count($topics['items']) ?></b> topics
                                    </li>
                                    <li>
                                        Last activity:<br><b><?= $last_activity['last_activity'] ?></b>
                                    </li>
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