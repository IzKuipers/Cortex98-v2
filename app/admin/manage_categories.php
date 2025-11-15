<?php

require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/forum.php");

verify_loggedin(true);

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["command"])) {
    switch ($_POST["command"]) {
        case "new":
            if (isset($_POST["name"], $_POST["description"])) {
                create_category($_POST["name"], $_POST["description"]);
            }
            break;
        case "delete":
            if (isset($_POST["id"])) {
                delete_category($_POST["id"]);
            }
            break;
    }
}

$session = get_user_from_session();
$categories = get_all_categories();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title>Manage Categories - Administration - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <table border="0" cellpadding="2" cellspacing="2" width="700">
            <td width="450" valign="top">
                <table border="0" cellpadding="4" cellspacing="0" bgcolor="#ffeeee" width="100%">
                    <thead>
                        <tr bgcolor="#ffcccc">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories["items"] as $category): ?>
                            <tr>
                                <td><?= $category["id"] ?></td>
                                <td><?= $category["name"] ?></td>
                                <td><?= $category["description"] ?></td>
                                <td align="right">
                                    <form action="" method="post" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $category["id"] ?>">
                                        <input type="hidden" name="command" value="delete">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
            <td width="250">
                <table border="0" cellpadding="2" cellspacing="2" bgcolor="#ffeeee" width="100%">
                    <tr nowrap align="left" bgcolor="#ffcccc">
                        <td>
                            <b>New category</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="command" value="new">
                                <table border="0" cellspacing="4">
                                    <tr>
                                        <td valign="top">
                                            Name
                                        </td>
                                        <td valign="top">
                                            <input type="text" name="name" style="width: 170px;">
                                        </td>
                                    </tr>
                                    <tr>

                                        <td valign="top">
                                            Description
                                        </td>
                                        <td valign="top">
                                            <textarea name="description" rows="4" style="width: 170px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td align="right">
                                            <input type="submit" value="Create">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </table>
    </center>
</body>

</html>