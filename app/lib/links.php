<?php

require_once("db.php");
require_once("session.php");

function get_all_links()
{
    $conn = connect_db();

    if (!$conn)
        return [];

    $statement = $conn->prepare("SELECT links.id, links.name, links.target, links.visits, links.description, users.username FROM links INNER JOIN users ON users.id = links.owner");

    if (!($statement->execute()))
        throw new Exception();

    $statement->bind_result($link_id, $name, $target, $visits, $description, $username);
    $result = [];

    while ($statement->fetch()) {
        $result[] = array(
            "id" => $link_id,
            "name" => $name,
            "target" => $target,
            "visits" => $visits,
            "description" => $description,
            "username" => $username,
        );
    }

    disconnect_db($conn, $statement);

    return $result;
}

function create_link(int $user_id, string $name, string $target, string $description = "")
{
    try {
        if (!$description)
            $description = "";

        $conn = connect_db();

        if (!$conn)
            return false;

        $statement = $conn->prepare("INSERT INTO links (owner,name,target,description) VALUES (?,?,?,?)");
        $statement->bind_param("isss", $user_id, $name, $target, $description);

        if (!$statement->execute())
            return false;

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function create_link_as_session(string $name, string $target, string $description = "")
{
    $session = get_user_from_session();

    if (!$session)
        return false;

    return create_link($session["id"], $name, $target, $description);
}

function delete_link(int $link_id)
{
    try {
        $conn = connect_db();

        if (!$conn)
            return false;

        $statement = $conn->prepare("DELETE FROM links WHERE id = ?");
        $statement->bind_param("i", $link_id);

        if (!$statement->execute())
            return false;

        return true;
    } catch (Exception $e) {
        return false;
    }
}