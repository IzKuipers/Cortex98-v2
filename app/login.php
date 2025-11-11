<?php

require_once("./components/headerbar.php");
require_once("lib/session.php");
require_once("lib/error.php");

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["username"], $_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $token = login($username, $password);

    if (!$token) {
        error_message("Login failed", "The username or password was incorrect. Please try again.", "login.php", "Try again");
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
    <link rel="stylesheet" href="css/login.css">
    <title>Log in to Cortex 98</title>
</head>

<body class="login-page">
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
                        <br>
                        <a href="register.php">Register</a>
                    </td>
                    <td align="right">
                        <br>
                        <button type="submit">Log in</button>
                    </td>
                </tr>
            </table>
        </form>
        <p style="color: gray;">A screen with a resolution of 800x600 and 256 colors is recommended.<br>This site is designed to work on Internet Explorer 5.<br><br>© 1999 IzKuipers. All Rights Reserved.</p>
    </center>
</body>

</html>