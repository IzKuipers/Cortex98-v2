<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/error.php");

function connect_db($offline_redirect = true): ?mysqli
{
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            throw new Exception();
        }

        return $conn;
    } catch (Exception $e) {
        if ($offline_redirect)
            we_are_offline();

        throw new Exception("Failed to connect to database");
    }
}

function disconnect_db($conn, ...$statements)
{
    foreach ($statements as $statement) {
        if (isset($statement) && $statement instanceof mysqli_stmt) {
            try {
                $statement->close();
            } catch (Exception $e) {
                error_log("Statement close was interrupted: " . $e->getMessage());
            }
        }
    }

    if (isset($conn) && $conn instanceof mysqli) {
        try {
            $conn->close();
        } catch (Exception $e) {
            error_log("Disconnect was interrupted: " . $e->getMessage());
        }
    }
}

function is_db_online(): bool
{
    try {
        $conn = connect_db();

        disconnect_db($conn);

        return true;
    } catch (Exception $e) {
        return false;
    }
}