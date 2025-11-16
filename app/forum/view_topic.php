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

$topic = get_topic_by_id($id);
$posts = get_topic_posts($id);
$activity = get_topic_last_activity($id);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title><?= $topic['title'] ?> - Forums - Cortex 98</title>
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
                                <img src="../assets/symbols/books.gif" alt="">
                                <a
                                    href="view_category.php?id=<?= $topic['topic']['category_id'] ?>"><?= $topic['topic']['category_name'] ?></a>
                                / <img src="../assets/symbols/book.gif" alt=""> <?= $topic['topic']['title'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if (count($posts['items'])): ?>
                                    <?php foreach ($posts['items'] as $post): ?>
                                        <?php
                                        $likes = get_post_like_count($post['id']);
                                        $has_liked = has_liked($post['id'], $session['id']);
                                        ?>
                                        <table border="0" cellpadding="2" width="100%">
                                            <tr bgcolor="#ffe680">
                                                <td>Post #<?= $post['id'] ?> by <?= $post['username'] ?></td>
                                                <td align="right" nowrap>
                                                    <?= date("d M Y, G:i:s", strtotime($post['created'])) ?>
                                                </td>
                                                <td align="right">
                                                    <a href="like_post.php?id=<?= $post['id'] ?>&continue=view_topic.php?id=<?= $id ?>"
                                                        style="text-decoration: none">
                                                        <img src="../assets/symbols/<?= $has_liked ? "heart-remove" : "heart-add" ?>.gif"
                                                            style="border: none" alt=""
                                                            title="<?= $has_liked ? "Unlike this post" : "Like this post" ?>">
                                                    </a>
                                                    <?= $likes['count'] ?> likes

                                                    <?php if (($post['id'] !== $topic['topic']['post_id']) && ($post['owner'] === $session['id']) || $session['admin']): ?>
                                                        -<a href="delete_post.php?id=<?= $post['id'] ?>&continue=view_topic.php?id=<?= $id ?>"
                                                            style="text-decoration: none">
                                                            <img src="../assets/symbols/trash.gif" style="border: none" alt=""
                                                                title="Delete this post">
                                                        </a>
                                                    <?php endif ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <p>
                                                        <?= str_replace("\n", "<br>", $post['content']) ?>
                                                    </p>
                                                </td>
                                                <td width="100">

                                                </td>
                                            </tr>
                                        </table>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <p style="margin: 10px 0;"><i>This topic has no posts!</i></p>
                                <?php endif ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="250" bgcolor="#eeeeff" valign="top">
                    <table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#eeeeff">
                        <tr bgcolor="#ccccff">
                            <td><img src="../assets/symbols/file.gif" alt=""><b>Statistics for this topic</b></td>
                        </tr>
                        <tr>
                            <td>
                                <ul>

                                    <li>
                                        Started by <b><?= $topic['topic']['username'] ?></b>
                                    </li>
                                    <li>
                                        Post count: <b><?= count($posts['items']) ?></b>
                                    </li>
                                    <li>
                                        Last active date:
                                        <b><?= date("d M Y", strtotime($activity['last_activity'])) ?></b>
                                    </li>
                                    <li>
                                        Last active time:
                                        <b><?= date("G:i:s", strtotime($activity['last_activity'])) ?></b>
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