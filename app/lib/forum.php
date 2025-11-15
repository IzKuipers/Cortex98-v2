<?php

require_once("db.php");

// CREATE

function create_category(string $name, string $description)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("INSERT INTO categories(name,description) VALUES (?,?)");
        $statement->bind_param("ss", $name, $description);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        return [
            "success" => true,
            "message" => "Category created successfully"
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function create_topic(int $category_id, int $user_id, string $title, string $content)
{
    try {
        $conn = connect_db();

        $topic_statement = $conn->prepare("INSERT INTO topic (owner, title, category) VALUES (?, ?)");
        $topic_statement->bind_param("isi", $user_id, $title, $category_id);

        if (!$topic_statement->execute())
            throw new Exception("Failed to create topic");

        $topic_id = $topic_statement->insert_id;
        $topic_statement->close();

        $post_statement = $conn->prepare("INSERT INTO posts (topic, owner, content) VALUES (?,?,?)");
        $post_statement->bind_param("iis", $topic_id, $user_id, $content);

        if (!$post_statement->execute())
            throw new Exception("Failed to create topic mainpost");

        $post_statement->close();

        return [
            "success" => true,
            "message" => "topic created successfully",
            "id" => $topic_id
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "id" => null
        ];
    } finally {
        disconnect_db($conn, $topic_statement, $post_statement);
    }
}

function create_post(int $topic_id, int $user_id, string $content)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("INSERT INTO posts(owner, topic, content) VALUES (?,?,?)");
        $statement->bind_param("iis", $user_id, $topic_id, $content);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        return [
            "success" => true,
            "message" => "Post created successfully",
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

//READ

function get_all_categories()
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT id,name,description FROM categories");

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        $statement->bind_result($category_id, $name, $description);

        $result = [];

        while ($statement->fetch()) {
            $result[] = array(
                "id" => $category_id,
                "name" => $name,
                "description" => $description,
            );
        }

        return [
            "success" => true,
            "message" => "Category retrieved successfully",
            "items" => $result
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "items" => []
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_category_topics(int $category_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT topic.id, topic.owner, users.username, topic.created, posts.content, topic.title FROM topic INNER JOIN users ON users.id = topic.owner INNER JOIN posts ON posts.id = topic.mainPost WHERE topic.category = ?;");
        $statement->bind_param("i", $category_id);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        $statement->bind_result($id, $owner, $username, $created, $content, $title);

        $result = [];

        while ($statement->fetch()) {
            $result[] = [
                "id" => $id,
                "owner" => $owner,
                "username" => $username,
                "created" => $created,
                "content" => $content,
                "title" => $title,
            ];
        }

        return [
            "success" => true,
            "message" => "Topics retrieved sucessfully",
            "items" => $result
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "items" => []
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_topic_posts(int $topic_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT id, owner, users.username, content, created FROM posts INNER JOIN users ON users.id = posts.id WHERE topic = ?");
        $statement->bind_param("i", $topic_id);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        $statement->bind_result($id, $owner, $username, $content, $created);
        $result = [];

        while ($statement->fetch()) {
            $result[] = [
                "id" => $id,
                "owner" => $owner,
                "username" => $username,
                "content" => $content,
                "created" => $created
            ];
        }

        return [
            "success" => true,
            "message" => "Posts retrieved successfully",
            "items" => $result
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "items" => []
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_user_stats(int $user_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT SUM(posts.id) as post_count, SUM(topic.id) as topic_count FROM posts INNER JOIN topic ON topic.owner = posts.owner WHERE posts.owner = ?;");
        $statement->bind_param("i", $user_id);

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        $statement->bind_result($post_count, $topic_count);
        $statement->fetch();

        return [
            "success" => true,
            "message" => "Statistics calculated successfully",
            "stats" => [
                "posts" => $post_count,
                "topics" => $topic_count
            ]
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "stats" => null
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_category_by_id(int $category_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT name,description FROM categories WHERE id = ?;");
        $statement->bind_param("i", $category_id);

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        $statement->bind_result($name, $description);
        $statement->fetch();

        if (!$name)
            throw new Exception("The specified category does not exist");

        return [
            "success" => true,
            "message" => "Category retrieved successfully",
            "name" => $name,
            "description" => $description
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "name" => null,
            "description" => null
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}


function get_all_topics()
{
    try {
        $conn = connect_db();
        $statement = $conn->prepare("SELECT topic.id, topic.owner, users.username, topic.created, posts.content, topic.title FROM topic INNER JOIN users ON users.id = topic.owner INNER JOIN posts ON posts.id = topic.mainPost;");

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        $statement->bind_result($id, $owner, $username, $created, $content, $title);

        $result = [];

        while ($statement->fetch()) {
            $result[] = [
                "id" => $id,
                "owner" => $owner,
                "username" => $username,
                "created" => $created,
                "content" => $content,
                "title" => $title,
            ];
        }

        return [
            "success" => true,
            "message" => "Topics retrieved sucessfully",
            "items" => $result
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "items" => []
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}


function get_all_posts()
{
    try {
        $conn = connect_db();
        $statement = $conn->prepare("SELECT posts.id, owner, users.username, content, created FROM posts INNER JOIN users ON users.id = posts.id");

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        $statement->bind_result($id, $owner, $username, $content, $created);
        $result = [];

        while ($statement->fetch()) {
            $result[] = [
                "id" => $id,
                "owner" => $owner,
                "username" => $username,
                "content" => $content,
                "created" => $created
            ];
        }

        return [
            "success" => true,
            "message" => "Posts retrieved successfully",
            "items" => $result
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "items" => []
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}
// UPDATE

function change_category_name(int $category_id, string $new_name)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $statement->bind_param("is", $category_id, $new_name);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        return [
            "success" => true,
            "message" => "Category updated successfully"
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function change_category_description(int $category_id, string $new_description)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("UPDATE categories SET description = ? WHERE id = ?");
        $statement->bind_param("is", $category_id, $new_description);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        return [
            "success" => true,
            "message" => "Category updated successfully"
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

// DELETE

function delete_category(int $category_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $statement->bind_param("i", $category_id);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        return [
            "success" => true,
            "message" => "Category deleted successfully",
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}
