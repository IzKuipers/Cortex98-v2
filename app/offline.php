<?php

require_once(__DIR__ . "/components/headerbar.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/main.css">
    <title>Offline! - Cortex 98</title>
</head>

<body>
    <center>
        <?= HeaderBar(true) ?>
        <table width="450" cellspacing="20" style="margin-top: 30px">
            <tr>
                <td valign="top">
                    <img src="assets/c98bsod.gif" alt="">
                </td>
                <td>
                    <table width="100%">
                        <tr>
                            <td>
                                <h1>We are offline!</h1>
                                <p>Sorry! It appears our database is offline. Maybe the web site is under maintanance. Please come back later.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>