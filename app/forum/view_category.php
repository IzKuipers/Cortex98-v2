`
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
        <table border="0" width="700" cellpadding="2" cellspacing="2">
            <tr>
                <td width="450" valign="top">
                    <table border="0" width="100%" cellpadding="2" cellspacing="2" bgcolor="#ffeeee">
                        <tr bgcolor="#ffcccc">
                            <td>
                                <a href="index.php">Forum</a> /
                                <img src="../assets/symbols/books.gif" alt=""> <?= $category['name'] ?> /
                                <img src="../assets/symbols/book.gif" alt=""> Topics
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <form action="new_topic.php" method="POST">
                                    <input type="hidden" name="category" value="<?= $id ?>">
                                    <table width="100%" border="0" cellpadding="2" cellspacing="2">
                                        <tr bgcolor="#ff8888">
                                            <td colspan="2" align="right"><b>New Topic</b></td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <b>Topic Title</b>
                                            </td>
                                            <td align="right">
                                                <input type="text" name="title" style="width: 250px;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <b>First message</b>
                                            </td>
                                            <td align="right">
                                                <textarea type="text" name="content" style="width: 250px;"
                                                    rows="4"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="right">
                                                <button type="submit">Post</button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>

                                <table width="100%" border="0" cellpadding="2" cellspacing="2">
                                    <tr bgcolor="#ff8888">
                                        <td colspan="2" align="right"><b>Existing topics</b></td>
                                    </tr>
                                    <?php foreach ($topics['items'] as $topic): ?>
                                        <tr>
                                            <td valign="top">
                                                <img src="../assets/book.gif" alt="">
                                            </td>
                                            <td>
                                                <h2>
                                                    <a href="view_topic.php?id=<?= $topic["id"] ?>">
                                                        <?= $topic['name'] ?>
                                                    </a>
                                                </h2>
                                                <p><?= $topic['content'] ?></p>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="250" bgcolor="#ffeeee" valign="top">
                    <div style="margin:10px">
                        <h1><?= $category['name'] ?></h1>
                        <p><?= $category['description'] ?></p>
                    </div>
                </td>
            </tr>

        </table>
    </center>
</body>

</html>