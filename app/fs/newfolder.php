<?php
require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../lib/fs.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/error.php");

verify_loggedin();
$session = get_user_from_session();

$continue = $_GET["continue"] ?? WEB_ROOT . "/files.php";

if (!isset($_GET["path"])) {
    error_message("Invalid link", "Sorry! This URL is not valid. You might've taken a wrong turn somewhere...", $continue);
    die;
}

$path = $_GET["path"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["foldername"])) {
    $folder_name = trim($_POST["foldername"]);
    if (!preg_match("/^[a-zA-Z0-9 ]+$/", $folder_name)) {
        error_message("Failed to create folder", "The folder name you specified contains characters we don't allow. Only alphanumeric characters or spaces are allowed.", $_SERVER['REQUEST_URI'], "Understood");
        die;
    }

    $result = $fs->createFolder($session["id"], $path . "/" . $_POST["foldername"]);

    if (!$result["success"]) {
        error_message("Failed to create folder", "The folder you entered could not be created. " . $result["message"], $continue, "Okay");
    }

    header("location: $continue");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/singleton.css">
    <title>New folder in <?= $path ?></title>
</head>

<body class="singleton-dialog">
    <center>
        <?= HeaderBar() ?>
        <p>Please enter a name for the folder you wish to create, then click <b>Create</b>.</p>
        <form action="" method="POST">
            <table width="300">
                <tr>
                    <td><b>Folder name:</b></td>
                    <td align="right"><input type="text" name="foldername"></td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <a href="<?= $continue ?>" style="vertical-align: middle">Cancel</a>
                    </td>
                    <td align="right">
                        <br>
                        <button type="submit">Create</button>
                    </td>
                </tr>
            </table>
        </form>
    </center>
</body>

</html>