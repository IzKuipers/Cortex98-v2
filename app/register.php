<?php

require_once("lib/user.php");
require_once("lib/error.php");

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["username"], $_POST["password"], $_POST["confirm"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password != $confirm) {
        echo "Passwords don't match.";
        die;
    }

    $result = create_user($username, $password);

    if ($result != CreateUserResult::Success) {
        $caption = $CreateUserResultCaptions[(int) $result];
        error_message("Registration failed", "$caption. Please try again.", "register.php");
        die;
    }
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
        <table>
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
                <td><b>Confirm:</b></td>
                <td><input type="password" name="confirm"></td>
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

</body>

</html>