<?php
require_once("./lib/session.php");
require_once("./components/headerbar.php");

verify_loggedin();
$session = get_user_from_session();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <title>Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
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
                    <table border="0" cellpadding="2" cellspacing="0" width="100%">
                        <tr bgcolor="#ccccff">
                            <td nowrap align="left" colspan="2"><b>Search</b></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" style="width: 100px">
                            </td>
                            <td align="right">
                                <button type="submit">Go</button>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="340" valign="top">
                    <table border="0" cellpadding="2" cellspacing="0" width="100%">
                        <tr bgcolor="#F8EB94">
                            <td align="left"><b>Cortex 98</b></td>
                            <td align="right">cortex98.nl</td>
                        </tr>
                        <tr>
                            <td>
                                <p>
                                    Welcome to Cortex 98! This site aims to provide useful resources to devices from the
                                    90s running Internet Explorer.
                                </p>
                                <p>You're currently logged in as: <b><?= $session["username"] ?></b>.</p>
                                <p>With this account you have full access to the web site. You can add links, upload
                                    installation files, and make use of our various tools.</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="180" valign="top" bgcolor="#ffffee">
                    <table border="0" cellpadding="2" cellspacing="0" width="100%">
                        <tr bgcolor="#ffffcc">
                            <td nowrap><b>Most clicked link</b></td>
                        </tr>
                        <tr>
                            <td>Every time someone clicks a link, we increment a little number. The most popular link
                                right now is...</td>
                        </tr>
                        <tr>
                            <td>
                                <ul>
                                    <li>FrogFind</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>This link has been clicked <b>3145</b> times!</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>