<?php

require_once(__DIR__ . "/lib/fs.php");
require_once(__DIR__ . "/lib/session.php");

verify_loggedin();
$session = get_user_from_session();

$path = "";

if (isset($_GET["path"]))
    $path = $_GET["path"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle file upload
    if (isset($_FILES['upload'])) {
        $result = $fs->uploadFile($session["id"], $path . ($path ? '/' : "") . $_FILES['upload']['name'], $_FILES['upload']);
        if ($result['success']) {
            echo "File uploaded with ID: " . $result['id'] . "\n";
        }
    } else if (isset($_POST["foldername"])) {
        $result = $fs->createFolder($session["id"], $path . ($path ? "/" : "") . $_POST["foldername"]);
        if ($result['success']) {
            echo "Folder created with ID: " . $result['id'] . "\n";
        }
    }
}

$contents = $fs->readFolder(relative_path: $path);

if ($contents["success"] !== true) {
    error_message("Failed to read folder", "The folder you tried to access could not be read. " . $contents["message"]);
    die;
}

?>

<form action="fsold.php?path=<?= $path ?>" method="POST" enctype="multipart/form-data">
    <input type="file" name="upload">
    <input type="submit" value="Upload">
</form>

<form action="fsold.php?path=<?= $path ?>" method="POST">
    <input type="text" name="foldername">
    <input type="submit" value="Create folder">
</form>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Path</th>
            <th>Owner</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <?php foreach ($contents["items"] as $id => $item): ?>

        <tr>
            <td><?= $item["name"] ?></td>
            <td><?= $item["relative_path"] ?></td>
            <td><?= $item["owner"] ?></td>
            <td><a href="#">Download</a></td>
            <td><a href="#">Delete</a></td>
        </tr>

    <?php endforeach; ?>

</table>