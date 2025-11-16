<?php

require_once(__DIR__ . "/../components/headerbar.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/main.css">
    <title>Logged out - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar() ?>
        <table width="450" cellspacing="20" style="margin-top: 30px">
            <tr>
                <td valign="top">
                    <img src="../assets/c98bsod.gif" alt="">
                </td>
                <td>
                    <table width="100%">
                        <tr>
                            <td>
                                <h1>You've been logged out</h1>
                                <p>Sad to see you go! Don't worry, you can always log back in to regain access to Cortex
                                    98.</p>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <br>
                                <a href="../login.php">Log back in</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>