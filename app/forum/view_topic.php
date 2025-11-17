<?php
require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin();
$session = get_user_from_session();
$is_admin = $session['admin'];

$id = $_GET["id"] ?? -1;

if ($id < 0) {
    header("location:index.php");
    die;
}

$topic = get_topic_by_id($id);
$posts = get_topic_posts($id);
$activity = get_topic_last_activity($id);
$is_locked = $topic['topic']['locked'] != 0;

function get_post_by_id(int $post_id): array|null
{
    global $posts;

    foreach ($posts['items'] as $post) {
        if ($post['id'] === $post_id)
            return (array) $post;
    }

    return null;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title><?= $topic['topic']['title'] ?> - <?= $topic['topic']['category_name'] ?> - Forums - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <table border="0" width="700" cellpadding="2" cellspacing="2">
            <script type="text/javascript">
                function toggleReplyForm(id) {
                    var form = document.all['replyform_' + id];
                    var button = document.all['replyform_button_' + id];

                    if (form == undefined || form == null) return;

                    var display = document.all["replyform_" + id].style.display;

                    if (display == 'none') {
                        button.innerHTML = "Reply «";
                        form.style.display = "block";
                    } else {
                        button.innerHTML = "Reply »";
                        form.style.display = "none";
                    }
                }
            </script>
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
                                        <table border="0" cellspacing="0" cellpadding="2" cellpadding="0" width="100%" style="background-color: #ffefae; margin-bottom: 10px;">
                                            <tr bgcolor="#f3d663">
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

                                                    <?php if (can_delete_post($post, $topic, $session)): ?>
                                                        -<a href="delete_post.php?id=<?= $post['id'] ?>&continue=forum/view_topic.php?id=<?= $id ?>"
                                                            style="text-decoration: none">
                                                            <img src="../assets/symbols/trash.gif" style="border: none" alt=""
                                                                title="Delete this post">
                                                        </a>
                                                    <?php endif ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <p style="margin:5px;">
                                                        <?= str_replace("\n", "<br>", $post['content']) ?>
                                                    </p>
                                                </td>
                                                <td width="100" align="right" valign="bottom">
                                                    <?php if (!$is_locked): ?>
                                                        <button onclick="javascript:toggleReplyForm(<?= $post['id'] ?>)"
                                                            id="replyform_button_<?= $post['id'] ?>">Reply »</button>
                                                    <?php endif ?>
                                                </td>
                                            </tr>
                                            <?php if (!$is_locked): ?>
                                                <tr id="replyform_<?= $post['id'] ?>" style="display: none;">
                                                    <td colspan="3">
                                                        <form method="POST"
                                                            action="new_post.php?topic_id=<?= $id ?>&reply_id=<?= $post['id'] ?>&continue=forum/view_topic.php?id=<?= $id ?>">
                                                            <table width="100%">
                                                                <tr bgcolor="#ffe680">
                                                                    <td colspan="2"><b>Reply</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right">
                                                                        <textarea name="content" id="" style="width: 250px;"
                                                                            lines="4"></textarea>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right">
                                                                        <button type="submit">Reply</button>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endif ?>
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
                            <?php if ($is_admin): ?>
                                <td align="right">
                                    <?php if ($is_locked): ?>
                                        <a style="text-decoration: none;"
                                            href="<?= WEB_ROOT ?>/admin/unlock_topic.php?id=<?= $id ?>&continue=forum/view_topic.php?id=<?= $id ?>">
                                            <img src="../assets/symbols/unlock.gif" alt="" style="border: none;">
                                        </a>
                                    <?php else: ?>
                                        <a style="text-decoration: none;"
                                            href="<?= WEB_ROOT ?>/admin/lock_topic.php?id=<?= $id ?>&continue=forum/view_topic.php?id=<?= $id ?>">
                                            <img src="../assets/symbols/lock.gif" alt="" style="border: none;">
                                        </a>
                                    <?php endif ?>
                                </td>
                            <?php endif ?>
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
                                    <?php if ($topic['topic']['locked']): ?>
                                        <li style="color: red;">
                                            This topic is <b>Locked</b>. No new posts can be sent here.
                                        </li>
                                    <?php endif ?>
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