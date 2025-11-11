<?php
require_once("./lib/session.php");
require_once("./lib/links.php");
require_once("./components/headerbar.php");
require_once("./components/navigation.php");

verify_loggedin();
$session = get_user_from_session();
$links = array_slice(get_all_links(), 0, 3);
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
                    <form action="search" method="GET">
                        <table border="0" cellpadding="2" cellspacing="0" width="100%">
                            <tr bgcolor="#ccccff">
                                <td nowrap align="left" colspan="2"><b>Search</b></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" style="width: 100px" name="q">
                                </td>
                                <td align="right">
                                    <button type="submit">Go</button>
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
                                <a href="#">Links</a>
                            </td>
                            <td align="right"><img src="assets/bullet5.gif" alt=""></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#">Files</a>
                            </td>
                            <td align="right"><img src="assets/bullet5.gif" alt=""></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#">GitHub Explorer</a>
                            </td>
                            <td align="right"><img src="assets/bullet5.gif" alt=""></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#">Release Downloader</a>
                            </td>
                            <td align="right"><img src="assets/bullet5.gif" alt=""></td>
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
                    <table border="0" cellpadding="2" cellspacing="0" width="100%" bgcolor="#ccffcc">
                        <tr bgcolor="#aaddaa">
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
                                                <img src="assets/boom.gif" alt="" style="vertical-align: middle">
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
                        <tr>
                            <td>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>