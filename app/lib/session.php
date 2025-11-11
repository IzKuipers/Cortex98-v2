<?php

require_once(__DIR__ . "/../../config.php");
require_once("db.php");
require_once("uuid.php");

function verify_loggedin()
{
    start_session_if_needed();

    if (!isset($_SESSION["token"])) {
        header("location: " . WEB_ROOT . "/login.php");
    }
}

function get_user_from_token(string $token): ?array
{
    $conn = connect_db();

    if (!$conn)
        return null;

    $statement = $conn->prepare("SELECT users.id,username,password,admin FROM tokens INNER JOIN users ON users.id = tokens.owner WHERE value = ?;");
    $statement->bind_param("s", $token);

    if (!$statement->execute())
        return null;

    $statement->bind_result($id, $username, $password, $admin);
    $statement->fetch();

    disconnect_db($conn, $statement);

    return [
        "username" => $username,
        "password" => $password,
        "id" => $id,
        "admin" => $admin
    ];
}

function get_user_from_session(): ?array
{
    start_session_if_needed();

    if (!isset($_SESSION["token"]))
        return null;

    return get_user_from_token($_SESSION["token"]);
}

function generate_token($userId): string
{
    $uuid = guidv4();

    $conn = connect_db();

    if (!$conn)
        return null;

    $statement = $conn->prepare("INSERT INTO tokens (owner,value) VALUES (?,?)");
    $statement->bind_param("is", $userId, $uuid);
    $statement->execute();

    disconnect_db($conn, $statement);

    return $uuid;
}

function delete_token_by_value(string $value): void
{
    $conn = connect_db();

    if (!$conn)
        return;

    try {
        $statement = $conn->prepare("DELETE FROM tokens WHERE value = ?");
        $statement->bind_param("s", $value);
        $statement->execute();
    } catch (Exception $e) {
        // Silently error...
    } finally {
        disconnect_db($conn, $statement);
    }

    return;
}

function login(string $username, string $password)
{
    $username = strtolower($username);
    start_session_if_needed();

    try {
        $conn = connect_db();

        if (!$conn)
            throw new Exception("Database connection failed");

        $statement = $conn->prepare("SELECT id,password FROM users WHERE username = ?");
        $statement->bind_param("s", $username);

        if (!$statement->execute())
            return null;

        $statement->bind_result($user_id, $hash);
        $statement->fetch();

        if (!$user_id)
            throw new Exception("No user with username " . $username);

        $valid = password_verify($password, $hash);

        if (!$valid)
            throw new Exception("Password is not valid");

        $token = generate_token($user_id);

        $_SESSION["token"] = $token;

        return $token;
    } catch (Exception $e) {
        return null;
    } finally {
        disconnect_db($conn, $statement);
    }
}

function logout()
{
    start_session_if_needed();

    if (!isset($_SESSION["token"]))
        return;

    delete_token_by_value($_SESSION["token"]);
    unset($_SESSION["token"]);

    header("location: " . WEB_ROOT . "/account/loggedout.php");
}

function start_session_if_needed()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}