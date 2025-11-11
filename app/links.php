<?php
require_once("lib/session.php");
require_once("components/headerbar.php");
require_once("components/navigation.php");
require_once("lib/links.php");

verify_loggedin();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["name"], $_POST["url"])) {
    $description = isset($_POST["description"]) ? $_POST["description"] : "";

    $result = create_link_as_session(htmlspecialchars($_POST["name"]), htmlspecialchars($_POST["url"]), htmlspecialchars($description));

    if (!$result) {
        error_message("Couldn't add link", "There might already be a link with that name, or the link is invalid. Please try something else.", "links.php");
    }
}

$session = get_user_from_session();
$links = get_all_links();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/links.css">
    <title>Links - Cortex 98</title>
</head>

<body class="links-page">
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <table border="0" cellpadding="2" cellspacing="2" width="700">
            <tr>
                <td width="450" valign="top">
                    <table width="100%" border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#ffffd2">
                        <tr bgcolor="#ffff92">
                            <td><b>Handy Dandy Links</b></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px;">
                                <?php foreach ($links as $link): ?>
                                    <div class="link">
                                        <p class="name"><?= $link["name"] ?></p>
                                        <a href="clicklink.php?linkid=<?= $link["id"] ?>"><?= $link["target"] ?></a>
                                        <?php if ($link["description"]): ?>
                                            <p class="description"><?= $link["description"] ?></p>
                                        <?php endif; ?>
                                        <p class="stats">
                                            <span class="clicks"><?= $link["visits"] ?> clicks</span> - Added by <?= $link["username"] ?>
                                            <?php if($link["username"] == $session["username"]): ?>
                                                - <a href="deletelink.php?linkid=<?=$link['id']?>">Delete</a>
                                            <?php endif; ?>
                                        </p>
                                        <hr>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </table>
                </td>

                <td width="250" valign="top">
                    <form action="" method="POST">
                        <table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#eeeeff">
                            <tr bgcolor="#ccccff">
                                <td nowrap align="left" colspan="2"><b>Add a link</b></td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td align="right">
                                    <input type="text" name="name">
                                </td>
                            </tr>
                            <tr>
                                <td>URL</td>
                                <td align="right">
                                    <input type="text" name="url">
                                </td>
                            </tr>
                            <tr>
                                <td>Description?</td>
                                <td align="right">
                                    <input type="text" name="description">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right">
                                    <button type="submit">Create</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>