<?php

require_once(__DIR__ . "/../config.php");
require_once("components/headerbar.php");
require_once("lib/user.php");
require_once("lib/error.php");

if (C98_DISABLE_REGISTRATION) {
    error_message("Registration disabled", "Sorry! Registration is currently disabled. This might be because of maintenance or a server problem somewhere. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["username"], $_POST["password"], $_POST["confirm"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password != $confirm) {
        error_message("Registration failed", "The passwords you entered don't match up. Please try again.", "register.php");
        die;
    }

    $result = create_user($username, $password);

    if ($result != CreateUserResult::Success) {
        $caption = $CreateUserResultCaptions[(int) $result];
        error_message("Registration failed", "$caption. Please try again.", "register.php");
        die;
    }

    header("location:login.php");
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
        <p>Create an account to continue to Cortex 98. If you already have an account, click <b>Log in</b>.</p>
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
                    <td><b>Confirm:</b></td>
                    <td align="right"><input type="password" name="confirm"></td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <a href="login.php">Log in</a>
                    </td>
                    <td align="right">
                        <br>
                        <button type="submit">Register</button>
                    </td>
                </tr>
            </table>
        </form>
        <p style="color: gray;">A screen with a resolution of 800x600 and 256 colors is recommended.<br>This site is
            designed to work on Internet Explorer 5.<br><br>© 1999 IzKuipers. All Rights Reserved.</p>
    </center>
</body>

</html>