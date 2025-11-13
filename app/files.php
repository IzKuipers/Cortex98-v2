<?php

require_once(__DIR__ . "/components/headerbar.php");
require_once(__DIR__ . "/components/navigation.php");
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/fs.php");

verify_loggedin();

$path = $_GET["path"] ?? "";
$session = get_user_from_session();
$contents = $fs->readFolder($path);
$fs_usage = $fs->get_fs_size();

if (!$contents["success"]) {
    error_message("Failed to read folder", "The folder you tried to access could not be read. " . $contents["message"], "files.php");
}

$split = explode("/", $path);
function generatePath(string $crumb, string $I)
{
    global $split;
    $localSplit = $split;

    return ltrim(join("/", array_splice($localSplit, 0, $I)) . "/" . $crumb, "/");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <title><?= !$path ? "Files" : $path ?> - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <table cellpadding="2" cellspacing="2" border="0" width="700">
            <tr>
                <td valign="top" width="500">
                    <table bgcolor="#ffffcc" width="100%" cellpadding="2" cellspacing="2" border="0">
                        <tr>
                            <td align="left" bgcolor="#ddddaa">
                                <a href="files.php">Files</a>
                                <?php foreach ($split as $index => $crumb): ?>
                                    / <a href="files.php?path=<?= generatePath($crumb, $index) ?>"><?= $crumb ?></a>
                                <?php endforeach ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="0" cellspacing="2" cellpadding="2" width="100%">
                                    <colgroup>
                                        <col>
                                        <col>
                                        <col>
                                        <col>
                                        <col align="right">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Author</th>
                                            <th>Size</th>
                                            <th>Kind</th>
                                            <th align="right"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contents["items"] as $index => $item): ?>
                                            <?php if ($item["type"] === "folder"): ?>
                                                <tr>
                                                    <td nowrap><img src="assets/symbols/folder.gif" alt="">
                                                        <a
                                                            href="files.php?path=<?= $path . ($path ? "/" : "") . $item["relative_path"] ?>"><?= $item["name"] ?></a>
                                                    </td>
                                                    <td nowrap><?= $item["username"] ?></td>
                                                    <td>-</td>
                                                    <td>Folder</td>
                                                    <td align="right">
                                                        <?php if ($session["id"] === $item["owner"] || $session["admin"]): ?>
                                                            <a href="fs/deletefile.php?id=<?= $item["id"] ?>&continue=<?= WEB_ROOT ?>/files.php?path=<?= $path ?>"
                                                                style="text-decoration: none;">
                                                                <img src="assets/symbols/trash.gif" alt="" border="0"
                                                                    title="Delete folder...">
                                                            </a>
                                                        <?php else: ?>
                                                            <a style="text-decoration: none;">
                                                                <img src="assets/symbols/trash-disabled.gif" alt="" border="0"
                                                                    title="The owner can delete this folder">
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <tr>

                                                    <td nowrap><img src="assets/symbols/file.gif" alt="">
                                                        <a href="fs/getfile.php?id=<?= $item["id"] ?>"><?= $item["name"] ?></a>
                                                    </td>
                                                    <td nowrap><?= $item["username"] ?></td>
                                                    <td><?= formatBytes($item["size"]) ?></td>
                                                    <td>File</td>
                                                    <td align="right">
                                                        <a href="fs/getfile.php?id=<?= $item["id"] ?>"
                                                            style="text-decoration: none;">
                                                            <img src="assets/symbols/floppydrive.gif" alt="" border="0"
                                                                title="Download">
                                                        </a>

                                                        <?php if ($session["id"] === $item["owner"] || $session["admin"]): ?>
                                                            <a href="fs/deletefile.php?id=<?= $item["id"] ?>&continue=<?= WEB_ROOT ?>/files.php?path=<?= $path ?>"
                                                                style="text-decoration: none;">
                                                                <img src="assets/symbols/trash.gif" alt="" border="0"
                                                                    title="Delete file...">
                                                            </a>
                                                        <?php else: ?>
                                                            <a style="text-decoration: none;">
                                                                <img src="assets/symbols/trash-disabled.gif" alt="" border="0"
                                                                    title="The owner can delete this file">
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" width="200">
                    <table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#eeeeff">
                        <tr bgcolor="#ccccff">
                            <td nowrap align="left" colspan="2"><b>Tasks</b></td>
                        </tr>
                        <tr>
                            <td>
                                <ul>
                                    <li>
                                        <a
                                            href="fs/upload.php?path=<?= $path ?>&continue=<?= WEB_ROOT ?>/files.php?path=<?= $path ?>">
                                            Upload file...
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="fs/newfolder.php?path=<?= $path ?>&continue=<?= WEB_ROOT ?>/files.php?path=<?= $path ?>">Create
                                            folder...</a>
                                    </li>
                                    <li>
                                        <a href="javascript:location.reload()">Refresh</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                    <table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#eeeeff">
                        <tr bgcolor="#ccccff">
                            <td nowrap align="left" colspan="2"><b>Storage Usage</b></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="progress-bar">
                                    <div class="inner" style="width: <?= (100 / FS_MAX_SIZE) * $fs_usage ?>%"></div>
                                </div>
                                <ul>
                                    <li>Used: <?= formatBytes($fs_usage) ?></li>
                                    <li>Free: <?= formatBytes(FS_MAX_SIZE - $fs_usage) ?></li>
                                    <li>Total: <?= formatBytes(FS_MAX_SIZE) ?></li>
                                </ul>
                                <!-- <progress max="<?= FS_MAX_SIZE ?>" value="<?= $fs_usage ?>"></progress> -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>