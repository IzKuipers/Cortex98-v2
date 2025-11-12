<?php

require_once(__DIR__ . "/../lib/db.php");
require_once(__DIR__ . "/../lib/session.php");

function get_user_stats()
{
    $session = get_user_from_session();

    if (!$session)
        return [
            "success" => false,
            "message" => "User is not logged in",
            "stats" => []
        ];

    $conn = connect_db();

    if (!$conn)
        return [
            "success" => false,
            "message" => "Failed to connect to database",
            "stats" => []
        ];

    try {

        $links_statement = $conn->prepare("SELECT COUNT(*) FROM links WHERE owner = ?");
        $links_statement->bind_param("i", $session["id"]);

        if (!$links_statement->execute())
            throw new Exception("Failed to get the links statistic");

        $links_statement->bind_result($links_count);
        $links_statement->fetch();
        $links_statement->close();

        $files_statement = $conn->prepare("SELECT COUNT(*) FROM fs WHERE owner = ? AND type = 'file'");
        $files_statement->bind_param("i", $session["id"]);

        if (!$files_statement->execute())
            throw new Exception("Failed to get the files statistic");

        $files_statement->bind_result($files_count);
        $files_statement->fetch();
        $files_statement->close();

        $folders_statement = $conn->prepare("SELECT COUNT(*) FROM fs WHERE owner = ? AND type = 'folder'");
        $folders_statement->bind_param("i", $session["id"]);

        if (!$folders_statement->execute())
            throw new Exception("Failed to get the folders statistic");

        $folders_statement->bind_result($folders_count);
        $folders_statement->fetch();
        $folders_statement->close();

        return [
            "success" => true,
            "message" => "Statistics retrieved successfully",
            "stats" => [
                "links" => $links_count,
                "files" => $files_count,
                "folders" => $folders_count,
            ]
        ];
    } catch (Exception $e) {
        echo $e;
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "stats" => [],
        ];
    } finally {
        disconnect_db($conn);
    }
}