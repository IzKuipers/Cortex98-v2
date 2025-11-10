<?php
require_once("./lib/session.php");

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
        <table  border="0" cellpadding="2" cellspacing="0" width="600">
            <tr>
                <td width="25%">
                    <img src="assets/c98banner.gif" alt="" height="32" width="250">
                </td>
                <td align="right" nowrap>
                    <span>Welcome, <b><?=$session["username"]?></b></span> - 
                    <a href="logout.php">Log out</a> -
                    <a href="account.php">Account</a>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>