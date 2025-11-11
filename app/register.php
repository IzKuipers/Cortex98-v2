<?php

require_once("components/headerbar.php");
require_once("lib/user.php");
require_once("lib/error.php");

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
    <link rel="stylesheet" href="css/login.css">
    <title>Log in to Cortex 98</title>
</head>

<body class="login-page">
    <center>
        <?= HeaderBar() ?>
        <p>Create an account to continue to Cortex 98. If you already have an account, click <b>Log in</b>.</p>
        <form action="" method="POST">
            <table width="300">
                <colgroup>
                    <col />
                    <col width="180" />
                </colgroup>
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
    </center>
</body>

</html>