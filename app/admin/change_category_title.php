<?php
require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/forum.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/error.php");

verify_loggedin(true);
$session = get_user_from_session();

$continue = $_GET["continue"] ?? WEB_ROOT . "/admin/manage_categories.php";

if (!isset($_GET["id"])) {
    error_message("Invalid link", "Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue);
    die;
}

$id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["newtitle"])) {
    $new_title = trim($_POST["newtitle"]);

    if (!$new_title) {
        error_message("Invalid title", "Please enter a new title for this category.", 'admin/change_category_title.php?id=' . $id . "&continue=" . $continue, 'Understood');
        die;
    }

    $result = change_category_name($id, $new_title);

    if (!$result["success"]) {
        error_message("Failed to rename category", "The category could not be renamed. " . $result["message"], $continue, "Okay");
    }

    header("location: $continue");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/singleton.css">
    <title>Change category title - Administration - Cortex 98</title>
</head>

<body class="singleton-dialog">
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <p>Please enter a new title for the category, then click <b>Rename</b>.</p>
        <form action="" method="POST">
            <table width="300">
                <tr>
                    <td><b>New title:</b></td>
                    <td align="right"><input type="text" name="newtitle"></td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <a href="<?= $continue ?>" style="vertical-align: middle">Cancel</a>
                    </td>
                    <td align="right">
                        <br>
                        <button type="submit">Rename</button>
                    </td>
                </tr>
            </table>
        </form>
    </center>
</body>

</html>