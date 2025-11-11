<?php

require_once("db.php");

function create_user(string $username, string $password): CreateUserResult
{
    $username = strtolower($username);
    $existing = get_user_by_name($username);

    if ($existing) return CreateUserResult::UserExists;

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        global $statement;

        $conn = connect_db();

        if (!$conn) return CreateUserResult::DbError;

        $statement = $conn->prepare("INSERT INTO users (username,password) VALUES (?,?)");
        $statement->bind_param("ss",$username, $hash);
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
}

$CreateUserResultCaptions = [
    1 => "A user with that name already exists",
    2 => "The user has been created successfully",
    3 => "A database error occurred. Please try again later",
];

function get_user_by_name(string $username)
{
    $username = strtolower($username);
    $conn = connect_db();

    if (!$conn)
        return null;

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

function delete_user_by_name(string $username) {
    $username = strtolower($username);
    $conn = connect_db();

    if (!$conn)
        return;

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