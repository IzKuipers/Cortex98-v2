<?php

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
    <title>Log in to Cortex 98</title>
</head>

<body>
    <form action="" method="POST">
        <table width="100%">
            <colgroup>
                <col />
                <col width="30%" />
            </colgroup>
            <tr>
                <td><b>Username:</b></td>
                <td><input type="text" name="username"></td>
            </tr>
            <tr>
                <td><b>Password:</b></td>
                <td><input type="password" name="password"></td>
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

</body>

</html>