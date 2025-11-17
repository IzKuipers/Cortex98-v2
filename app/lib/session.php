<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/db.php");
require_once(__DIR__ . "/uuid.php");

function verify_loggedin(bool $require_admin = false)
{
    start_session_if_needed();

    try {
        if (!isset($_SESSION["token"]))
            throw new Exception();

        $user = get_user_from_token($_SESSION["token"]);

        if (!$user)
            throw new Exception();

        if (C98_LOCKDOWN && !$user["admin"])
            throw new Exception();

        if ($require_admin && !$user["admin"]) {
            error_message(
                "Access denied",
                "You do not have permission to access this part of Cortex 98. Please contact an administrator if you believe this to be an error."
            );

            die;
        }
    } catch (Exception $e) {
        header("location: " . WEB_ROOT . "/login.php");
    }

}

function get_user_from_token(string $token): ?array
{
    try {
        $conn = connect_db();
    } catch (Exception $e) {
        return null;
    }

    $statement = $conn->prepare("SELECT users.id,username,password,admin FROM tokens INNER JOIN users ON users.id = tokens.owner WHERE value = ?;");
    $statement->bind_param("s", $token);

    if (!$statement->execute())
        return null;

    $statement->bind_result($id, $username, $password, $admin);
    $statement->fetch();

    if (!$id) return null;

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

    try {
        $conn = connect_db();
    } catch (Exception $e) {
        return null;
    }

    $statement = $conn->prepare("INSERT INTO tokens (owner,value) VALUES (?,?)");
    $statement->bind_param("is", $userId, $uuid);
    $statement->execute();

    disconnect_db($conn, $statement);

    return $uuid;
}

function delete_token_by_value(string $value): void
{
    try {
        $conn = connect_db();
    } catch (Exception $e) {
        return;
    }

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

        $statement = $conn->prepare("SELECT id,password,admin FROM users WHERE username = ?");
        $statement->bind_param("s", $username);

        if (!$statement->execute())
            return null;

        $statement->bind_result($user_id, $hash, $is_admin);
        $statement->fetch();

        if (C98_LOCKDOWN && !$is_admin) {
            throw new Exception("Cortex 98 is locked down! Only administrators can log in at this time.");
        }

        if (!$user_id)
            throw new Exception("No user with username " . $username);

        $valid = password_verify($password, $hash);

        if (!$valid)
            throw new Exception("Password is not valid");

        $token = generate_token($user_id);

        $_SESSION["token"] = $token;

        return [
            "success" => true,
            "message" => "Welcome back!",
            "token" => $token
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "token" => null
        ];
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