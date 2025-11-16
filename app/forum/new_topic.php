<?php

require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin();
$session = get_user_from_session();

$continue = $_GET["continue"] ?? WEB_ROOT . "/forum/index.php";

if (!isset($_GET["id"])) {
    error_message("Invalid link", "Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue);
    die;
}

$id = $_GET["id"];

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['title'], $_POST['content'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $result = create_topic(intval($id), $session['id'], $title, $content);

    if (!$result['success']) {
        error_message("Failed to create topic", "An error occurred while creating the topic. " . $result['message'], 'forum/view_category.php?id=' . $id);
        die;
    }

    $topic_id = $result['id'];

    header("Location: view_topic.php?id=$topic_id");
    die;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/singleton.css">
    <title>Create Topic - Forums - Cortex 98</title>
</head>

<body class="singleton-dialog">
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <form action="" method="POST">
            <table width="100%" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td valign="top" nowrap>
                        <b>Topic Title</b>
                    </td>
                    <td align="right">
                        <input type="text" name="title" style="width: 250px;">
                    </td>
                </tr>
                <tr>
                    <td valign="top" nowrap>
                        <b>First message</b>
                    </td>
                    <td align="right">
                        <textarea type="text" name="content" style="width: 250px;" rows="4"></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <button type="submit">Post</button>
                    </td>
                </tr>
            </table>
        </form>
    </center>
</body>

</html>