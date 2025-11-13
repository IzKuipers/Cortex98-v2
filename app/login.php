<?php

require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/components/headerbar.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/error.php");

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["username"], $_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = login($username, $password);

    if (!$result['success']) {
        error_message("Login failed", "We could not log you in. " . $result['message'], "login.php", "Try again");
        die;
    }

    header("location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/singleton.css">
    <title>Log in to Cortex 98</title>
</head>

<body class="singleton-dialog">
    <center>
        <?= HeaderBar() ?>
        <p>Please log in to continue to Cortex 98. If you don't yet have an account, click <b>Register</b>.</p>
        <form action="" method="POST">
            <table width="300">
                <tr>
                    <td><b>Username:</b></td>
                    <td align="right"><input type="text" name="username"></td>
                </tr>
                <tr>
                    <td><b>Password:</b></td>
                    <td align="right"><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td>
                        <?php if (!C98_DISABLE_REGISTRATION): ?>
                            <br>
                            <a href="register.php">Register</a>
                        <?php endif ?>
                    </td>
                    <td align="right">
                        <br>
                        <button type="submit">Log in</button>
                    </td>
                </tr>
            </table>
        </form>
        <p style="color: gray;">A screen with a resolution of 800x600 and 256 colors is recommended.<br>This site is
            designed to work on Internet Explorer 5 or newer.<br><br>© 1999 IzKuipers. All Rights Reserved.</p>
    </center>
</body>

</html>