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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["upload"])) {
    $result = $fs->uploadFile($session["id"], $path, $_FILES["upload"]);

    if (!$result["success"]) {
        error_message("Failed to upload file", "The file you're trying to upload couldn't be processed. " . $result["message"], $continue, "Okay");
        die;
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
    <title>Upload file to <?= $path ?></title>
</head>

<body class="singleton-dialog">
    <center>
        <?= HeaderBar() ?>
        <p id="test">Please choose the file you wish to upload, then click <b>Upload</b>.</p>
        <form action="" method="POST" enctype="multipart/form-data"
            onsubmit="document.all.test.innerHTML = 'Your file is being uploaded to the server, please wait...';">
            <table width="300">
                <tr>
                    <td colspan="2"><input width="300" style="width: 100%" type="file" name="upload" required></td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <a href="<?= $continue ?>" style="vertical-align: middle">Cancel</a>
                    </td>
                    <td align="right">
                        <br>
                        <button type="submit">Upload</button>
                    </td>
                </tr>
            </table>
        </form>
    </center>
</body>

</html>