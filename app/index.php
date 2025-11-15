<?php
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/links.php");
require_once(__DIR__ . "/lib/fs.php");
require_once(__DIR__ . "/account/stats.php");
require_once(__DIR__ . "/components/headerbar.php");
require_once(__DIR__ . "/components/navigation.php");

$start_time = date_create()->format('Uv');

verify_loggedin();

$session = get_user_from_session();
$links = array_slice(get_all_links()["items"], 0, 3);
$root = $fs->readFolder();
$stats = get_user_stats();

$end_time = date_create()->format('Uv') - $start_time;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/main.css">
    <title>Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <?= NavigationBar() ?>
        <table border="0" cellpadding="2" cellspacing="2" width="700">
            <tr>
                <td width="180" valign="top" bgcolor="#eeeeff">
                    <form action="http://frogfind.com" method="GET">
                        <table border="0" cellpadding="2" cellspacing="0" width="100%">
                            <tr bgcolor="#ccccff">
                                <td nowrap align="left" colspan="2"><b>Frogfind</b></td>
                            </tr>
                            <tr>
                                <td valign="middle" align="left">
                                    <input type="text" style="width: 110px" name="q">
                                </td>
                                <td valign="middle" align="right">
                                    <button type="submit">Ribbit!</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <table border="0" cellpadding="2" cellspacing="0" width="100%">
                        <tr bgcolor="#ccccff">
                            <td nowrap align="left" colspan="2"><b>Tools</b></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="github.php">GitHub Explorer</a>
                            </td>
                            <td align="right"><img src="assets/symbols/bullet5.gif" alt=""></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="downloader.php">Downloader</a>
                            </td>
                            <td align="right"><img src="assets/symbols/bullet5.gif" alt=""></td>
                        </tr>


                    </table>
                    <hr>
                    <table border="0" cellpadding="2" cellspacing="0" width="100%">
                        <tr>
                            <td>
                                Copyright © 1999 IzKuipers. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="340" valign="top">
                    <table border="0" cellpadding="2" cellspacing="0" width="100%" bgcolor="#ffb5a2"
                        class="welcome-table">
                        <tr bgcolor="#ff8364">
                            <td align="left"><b>Cortex 98</b></td>
                            <td align="right">cortex98.nl</td>
                        </tr>
                        <tr>
                            <td>
                                <p>
                                    Welcome to Cortex 98! This site aims to provide useful resources to devices from
                                    the
                                    90s running Internet Explorer.
                                </p>
                                <p>You're currently logged in as: <b><?= $session["username"] ?></b>.</p>
                                <p>With this account you have full access to the web site. You can add links, upload
                                    installation files, and make use of our various tools.</p>
                                <img src="assets/c98badge.gif" alt="">
                                <img src="assets/freeie.gif" alt="">
                                <img src="assets/flagbadge.gif" alt="">
                            </td>
                        </tr>
                    </table>
                    <table border="0" cellpadding="2" cellspacing="0" width="100%" bgcolor="#ffccff">
                        <tr bgcolor="#ddaadd">
                            <td><b>Root folders</b></td>
                        </tr>
                        <tr>
                            <td>
                                <p>These are the top-level folders of Cortex 98:</p>
                                <table border="0" cellspacing="2" cellpadding="2">
                                    <?php foreach ($root["items"] as $index => $item): ?>
                                        <?php if ($item["type"] === "folder"): ?>
                                            <tr>
                                                <td>
                                                    <img src="assets/symbols/folder.gif" alt="">
                                                </td>
                                                <td>
                                                    <a href="files.php?path=<?= $item["name"] ?>"><?= $item["name"] ?></a>
                                                </td>
                                            </tr>
                                        <?php endif ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <a href="files.php">View all files ></a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="180" valign="top" bgcolor="#ffffd2">
                    <table border="0" cellpadding="2" cellspacing="0" width="100%">
                        <tr bgcolor="#ffff92">
                            <td nowrap><b>Popular links</b></td>
                        </tr>
                        <tr>
                            <td>These are the top 3 most visited links:</td>
                        </tr>
                        <tr>
                            <td>
                                <ul>
                                    <?php foreach ($links as $index => $link): ?>
                                        <?php if ($index === 0): ?>
                                            <li>
                                                <?= $index + 1 ?>.
                                                <b>
                                                    <a href="clicklink.php?linkid=<?= $link["id"] ?>"><?= $link["name"] ?></a>
                                                </b>
                                                <img src="assets/symbols/boom.gif" alt="" style="vertical-align: middle">
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <?= $index + 1 ?>.
                                                <a href="clicklink.php?linkid=<?= $link["id"] ?>">
                                                    <?= $link["name"] ?>
                                                </a>
                                            </li>
                                        <?php endif ?>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><a href="links.php">View all links ></a></td>
                        </tr>
                    </table>
                    <table border="0" cellpadding="2" cellspacing="0" width="100%">
                        <tr bgcolor="#ffff92" nowrap>
                            <td>
                                <b>Statistics</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                You're the owner of:
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <ul>
                                    <li><img src="assets/symbols/mouse.gif" alt="">
                                        <b><?= $stats["stats"]["links"] ?></b> links
                                    </li>
                                    <li><img src="assets/symbols/file.gif" alt="">
                                        <b><?= $stats["stats"]["files"] ?></b> files
                                    </li>
                                    <li><img src="assets/symbols/folder.gif" alt="">
                                        <b><?= $stats["stats"]["folders"] ?></b> folders
                                    </li>
                                    <li><img src="assets/symbols/keyboard.gif" alt=""> <b>-</b> posts</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>This page's data was retrieved in <b><?= $end_time ?>ms</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <p style="color: gray;">
            The administrators of this web site reserve the right to remove inappropriate or illegal content. <a
                href="legal.php">Legal</a>
        </p>
    </center>
</body>

</html>