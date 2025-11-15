<?php

require_once(__DIR__ . "/db.php");
require_once(__DIR__ . "/../../config.php");

function create_user(string $username, string $password): CreateUserResult
{
    if (C98_DISABLE_REGISTRATION) {
        return CreateUserResult::RegistrationDisabled;
    }

    $username = strtolower($username);
    $existing = get_user_by_name($username);

    if ($existing)
        return CreateUserResult::UserExists;

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        global $statement;

        $conn = connect_db();

        $statement = $conn->prepare("INSERT INTO users (username,password) VALUES (?,?)");
        $statement->bind_param("ss", $username, $hash);
        $statement->execute();

        return CreateUserResult::Success;
    } catch (Exception $e) {
        return CreateUserResult::DbError;
    } finally {
        disconnect_db($conn, $statement);
    }
}

enum CreateUserResult: int
{
    case UserExists = 1;
    case Success = 2;
    case DbError = 3;
    case RegistrationDisabled = 4;
}

$CreateUserResultCaptions = [
    1 => "A user with that name already exists",
    2 => "The user has been created successfully",
    3 => "A database error occurred. Please try again later",
    4 => "Registration is turned off right now! Please try again later"
];

function get_user_by_name(string $username)
{
    $username = strtolower($username);

    try {
        $conn = connect_db();
    } catch (Exception $e) {
        return null;
    }

    try {
        $statement = $conn->prepare("SELECT id,password,admin FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $statement->bind_result($id, $password, $admin);
        $statement->fetch();

        if (!$id)
            return null;

        return [
            "username" => $username,
            "password" => $password,
            "id" => $id,
            "admin" => $admin
        ];
    } catch (Exception $e) {
        return null;
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_all_users()
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT id,username,admin FROM users");

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        $statement->bind_result($id, $username, $admin);
        $result = [];

        while ($statement->fetch()) {
            $result[] = [
                "id" => $id,
                "username" => $username,
                "admin" => $admin
            ];
        }

        return [
            "success" => true,
            "message" => "Users retrieved successfully",
            "users" => $result
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "users" => []
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function delete_user_by_name(string $username)
{
    $username = strtolower($username);

    try {
        $conn = connect_db();
    } catch (Exception $e) {
        return;
    }

    try {
        $statement = $conn->prepare("DELETE FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
    } catch (Exception $e) {
        // Silently error...
    } finally {
        disconnect_db($conn, $statement);
    }

    return;
}