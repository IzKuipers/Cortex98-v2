<?php

require_once("db.php");
require_once("session.php");

function get_all_links()
{
    $conn = connect_db();

    if (!$conn)
        return [];

    $statement = $conn->prepare("SELECT links.id, links.name, links.target, links.visits, links.description, users.username FROM links INNER JOIN users ON users.id = links.owner ORDER BY links.visits DESC");

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

function get_link_by_id(int $link_id)
{
    try {
        global $conn, $statement;

        $conn = connect_db();

        if (!$conn)
            return null;

        $statement = $conn->prepare("SELECT links.id, links.name, links.target, links.visits, links.description, users.username FROM links INNER JOIN users ON users.id = links.owner WHERE links.id = ?");
        $statement->bind_param("i", $link_id);

        if (!($statement->execute()))
            throw new Exception();

        $statement->bind_result($link_id, $name, $target, $visits, $description, $username);
        $statement->fetch();

        if (!$link_id)
            return null;

        return [
            "id" => $link_id,
            "name" => $name,
            "target" => $target,
            "visits" => $visits,
            "description" => $description,
            "username" => $username,
        ];
    } catch (Exception $e) {
        return null;
    } finally {
        disconnect_db($conn, $statement);
    }
}

function create_link(int $user_id, string $name, string $target, string $description = "")
{
    if (!filter_var($target, FILTER_VALIDATE_URL)) {
        return false;
    }

    try {
        global $conn, $statement;

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
    } finally {
        disconnect_db($conn, $statement);
    }
}

function create_link_as_session(string $name, string $target, string $description = "")
{
    $session = get_user_from_session();

    if (!$session)
        return false;

    return create_link($session["id"], $name, $target, $description);
}

function delete_link(int $link_id): true|string
{
    try {
        global $conn, $statement;
        $conn = connect_db();

        if (!$conn)
            throw new Exception("Could not connect to the database.");

        $statement = $conn->prepare("DELETE FROM links WHERE id = ?");
        $statement->bind_param("i", $link_id);

        if (!$statement->execute())
            throw new Exception("The query failed to execute.");

        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    } finally {
        disconnect_db($conn, $statement);
    }
}

function delete_link_as_session(int $link_id): true|string
{
    try {
        $existing = get_link_by_id($link_id);

        $session = get_user_from_session();

        if (!$session || !$existing)
            throw new Exception("The link doesn't exist or you're not logged in.");
        if ($session["username"] != $existing["username"] && !$session["admin"])
            throw new Exception("You are not the owner of this link.");

        return delete_link($link_id);
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function click_link(int $link_id)
{
    $link = get_link_by_id($link_id);

    if (!$link)
        return;

    $clicks = $link["visits"] + 1;
    $conn = connect_db();

    if (!$conn)
        return;

    $statement = $conn->prepare("UPDATE links SET visits = ? WHERE id = ?");
    $statement->bind_param("ii", $clicks, $link_id);
    $statement->execute();

    header("location:" . $link["target"]);
}