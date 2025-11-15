<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../components/headerbar.php");
require_once(__DIR__ . "/../components/navigation.php");
require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/user.php");
require_once(__DIR__ . "/../lib/forum.php");
require_once(__DIR__ . "/../lib/links.php");
require_once(__DIR__ . "/../lib/fs.php");

verify_loggedin(true);

$session = get_user_from_session();
$warnings = [];

$users = get_all_users();
$links = get_all_links();
$categories = get_all_categories();
$topics = get_all_topics();
$posts = get_all_posts();
$files = $fs->get_all_entries();
$fs_usage = $fs->get_fs_size() ?? 0;

if (!$users['success'])
    $warnings[] = "Failed to get user list: " . $users['message'];

if (!$links['success'])
    $warnings[] = "Failed to get links: " . $links['message'];

if (!$categories['success'])
    $warnings[] = "Failed to get forum categories: " . $categories['message'];

if (!$topics['success'])
    $warnings[] = "Failed to get forum topics: " . $topics['message'];

if (!$posts['success'])
    $warnings[] = "Failed to get forum posts: " . $posts['message'];

if (!$files['success'])
    $warnings[] = "Failed to get filesystem entries: " . $files['message'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title>Administration - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>

        <?php if (count($warnings) > 0): ?>
            <table border="0" bgcolor="#ffde9d" cellpadding="2" cellspacing="2" width="700">
                <colgroup>
                    <col width="20px">
                </colgroup>
                <tr>
                    <td valign="top">
                        <img src="../assets/symbols/warning.gif" alt="">
                    </td>
                    <td>
                        <?php foreach ($warnings as $warning): ?>
                            <?= $warning ?><br>
                        <?php endforeach ?>
                    </td>
                </tr>
            </table>
        <?php endif; ?>

        <table border="0" cellpadding="2" cellspacing="2" width="700">
            <td width="500" valign="top">
                <table border="0" cellpadding="4" cellspacing="0" bgcolor="#ffeeee" width="100%">
                    <thead>
                        <tr bgcolor="#ffcccc">
                            <th>ID</th>
                            <th>Username</th>
                            <th>Admin?</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users["users"] as $user): ?>
                            <tr>
                                <td><?= $user["id"] ?></td>
                                <td><?= $user["username"] ?></td>
                                <td><?= $user["admin"] ? "Yes" : "No" ?></td>
                                <td align="right"><a href="deleteuser.php?id=<?= $user["id"] ?>">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
            <td width="200" valign="top">
                <table border="0" cellpadding="2" cellspacing="2" bgcolor="#eeeeff" width="100%">
                    <tr nowrap align="left" bgcolor="#ccccff">
                        <td>
                            <b>Tasks</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li>
                                    <a href="manage_categories.php">Manage forum categories</a>
                                </li>
                                <li>
                                    <a href="logout_everybody.php">Log out everybody</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="2" cellspacing="2" bgcolor="#eeeeff" width="100%">
                    <tr nowrap align="left" bgcolor="#ccccff">
                        <td>
                            <b>Statistics</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li>
                                    <b><?= count($users["users"]) ?></b> users
                                </li>
                                <li>
                                    <b><?= count($links["items"]) ?></b> links
                                </li>
                                <li>
                                    <b><?= count($categories["items"]) ?></b> categories
                                </li>
                                <li>
                                    <b><?= count($topics["items"]) ?></b> topics
                                </li>
                                <li>
                                    <b><?= count($posts["items"]) ?></b> posts
                                </li>
                                <li>
                                    <b><?= count($files["items"]) ?></b> file entries
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="2" cellspacing="2" bgcolor="#eeeeff" width="100%">
                    <tr nowrap align="left" bgcolor="#ccccff">
                        <td>
                            <b>Environment</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li>
                                    Lockdown: <?= C98_LOCKDOWN ? "Enabled!" : "Disabled" ?>
                                </li>
                                <li>
                                    Registration: <?= C98_DISABLE_REGISTRATION ? "Disabled!" : "Enabled" ?>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="2" cellspacing="2" bgcolor="#eeeeff" width="100%">
                    <tr nowrap align="left" bgcolor="#ccccff">
                        <td>
                            <b>Filesystem usage</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php if ($fs_usage > 0): ?>
                                <div class="progress-bar">
                                    <div class="inner" style="width: <?= (100 / FS_MAX_SIZE) * $fs_usage ?>%"></div>
                                </div>
                                <ul>
                                    <li>Used: <?= formatBytes($fs_usage) ?></li>
                                    <li>Free: <?= formatBytes(FS_MAX_SIZE - $fs_usage) ?></li>
                                    <li>Total: <?= formatBytes(FS_MAX_SIZE) ?></li>
                                </ul>
                                <!-- <progress max="<?= FS_MAX_SIZE ?>" value="<?= $fs_usage ?>"></progress> -->
                            <?php else: ?>
                                <img src="../assets/symbols/warning.gif" alt=""> Failed to get storage usage information.
                                The
                                filesystem might still be empty, or there's a problem with the database.
                            <?php endif ?>
                        </td>
                    </tr>
                </table>
            </td>
        </table>
    </center>
</body>

</html>