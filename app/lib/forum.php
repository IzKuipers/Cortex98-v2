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

        $topic_statement = $conn->prepare("INSERT INTO topic (owner, title, category) VALUES (?,?,?)");
        $topic_statement->bind_param("isi", $user_id, $title, $category_id);

        if (!$topic_statement->execute())
            throw new Exception("Failed to create topic");

        $topic_id = $topic_statement->insert_id;
        $topic_statement->close();

        $post_statement = $conn->prepare("INSERT INTO posts (topic, owner, content) VALUES (?,?,?)");
        $post_statement->bind_param("iis", $topic_id, $user_id, $content);

        if (!$post_statement->execute())
            throw new Exception("Failed to create topic mainpost");

        $post_id = $post_statement->insert_id;
        $post_statement->close();

        $update_statement = $conn->prepare("UPDATE topic SET mainPost = ? WHERE id = ?");
        $update_statement->bind_param("ii", $post_id, $topic_id);

        if (!$update_statement->execute())
            throw new Exception("Failed to update topic to set mainpost");

        $update_statement->close();

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
        disconnect_db($conn);
    }
}

function create_post(int $topic_id, int $user_id, string $content, ?int $reply_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("INSERT INTO posts(owner, topic, content, repliesTo) VALUES (?,?,?,?)");
        $statement->bind_param("iisi", $user_id, $topic_id, $content, $reply_id);

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

        $statement = $conn->prepare("SELECT t.id, t.owner, u.username, t.created, p.content, t.title FROM topic t JOIN users u ON u.id = t.owner LEFT JOIN posts p ON p.id = t.mainPost WHERE t.category = ?;");
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

        $statement = $conn->prepare("SELECT posts.id, posts.owner, posts.repliesTo, users.username, content, created FROM posts INNER JOIN users ON users.id = posts.owner WHERE topic = ? ORDER BY created DESC;");
        $statement->bind_param("i", $topic_id);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        $statement->bind_result($id, $owner, $repliesTo, $username, $content, $created);
        $result = [];

        while ($statement->fetch()) {
            $result[] = [
                "id" => $id,
                "owner" => $owner,
                "username" => $username,
                "content" => $content,
                "created" => $created,
                "replies_to" => $repliesTo
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
        $statement = $conn->prepare("SELECT t.id, u.username, t.title, p.id AS post_id, p.content, p.created, locked FROM topic t JOIN users u ON u.id = t.owner JOIN posts p ON p.topic = t.id;");

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        $statement->bind_result($id, $owner, $username, $created, $content, $title, $locked);

        $result = [];

        while ($statement->fetch()) {
            $result[] = [
                "id" => $id,
                "owner" => $owner,
                "username" => $username,
                "created" => $created,
                "content" => $content,
                "title" => $title,
                "locked" => $locked
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

function get_category_last_activity(int $category_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT GREATEST( COALESCE(MAX(p.created), '0000-00-00 00:00:00'), COALESCE(MAX(t.created), '0000-00-00 00:00:00') ) AS last_activity FROM topic t LEFT JOIN posts p ON p.topic = t.id WHERE t.category = ?;");
        $statement->bind_param("i", $category_id);

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        $statement->bind_result($last_activity);
        $statement->fetch();

        if (!$last_activity)
            throw new Exception("The specified category does not exist.");

        return [
            'success' => true,
            'message' => "Category statistic retrieved successfully",
            'last_activity' => $last_activity
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'last_activity' => null
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_topic_last_activity(int $topic_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT GREATEST(COALESCE(MAX(p.created), '0000-00-00 00:00:00'), COALESCE(t.created, '0000-00-00 00:00:00')) AS last_activity FROM topic t LEFT JOIN posts p ON p.topic = t.id WHERE t.id = ?;");
        $statement->bind_param("i", $topic_id);

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        $statement->bind_result($last_activity);
        $statement->fetch();

        if (!$last_activity)
            throw new Exception("The specified topic does not exist.");

        return [
            'success' => true,
            'message' => "Topic statistic retrieved successfully",
            'last_activity' => $last_activity
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'last_activity' => null
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_topic_by_id(int $topic_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT u.username, t.owner, t.title, p.id AS post_id, p.content, p.created, c.id, c.name, t.locked FROM topic t JOIN users u ON u.id = t.owner JOIN posts p ON p.topic = t.id JOIN categories c ON t.category = c.id WHERE t.id = ?;");
        $statement->bind_param('i', $topic_id);

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        $statement->bind_result($username, $owner, $title, $post_id, $content, $created, $category_id, $category_name, $locked);
        $statement->fetch();

        if (!$username)
            throw new Exception("The specified topic does not exist.");

        return [
            'success' => true,
            'message' => "Topic retrieved successfully",
            'topic' => [
                "username" => $username,
                "owner" => $owner,
                "title" => $title,
                "post_id" => $post_id,
                "content" => $content,
                "created" => $created,
                "category_id" => $category_id,
                "category_name" => $category_name,
                "locked" => $locked
            ]
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'topic' => []
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function get_post_like_count(int $post_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post = ?;");
        $statement->bind_param("i", $post_id);

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        $statement->bind_result($like_count);
        $statement->fetch();

        return [
            "success" => true,
            "message" => "Retrieved like count successfully",
            "count" => $like_count ?? 0
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage(),
            "count" => 0
        ];
    } finally {
        disconnect_db($conn, $statement);
    }
}

function has_liked(int $post_id, int $user_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post = ? AND owner = ?;");
        $statement->bind_param("ii", $post_id, $user_id);

        if (!$statement->execute())
            throw new Exception();

        $statement->bind_result($like_count);
        $statement->fetch();

        if ($like_count > 0)
            return true;
    } catch (Exception $e) {
        return false;
    } finally {
        disconnect_db($conn, $statement);
    }
}

function like_post(int $post_id, int $user_id)
{
    $has_liked = has_liked($post_id, $user_id);

    if ($has_liked)
        return false;

    try {
        $conn = connect_db();

        $statement = $conn->prepare("INSERT INTO likes (owner, post) VALUES (?,?)");
        $statement->bind_param("ii", $user_id, $post_id);
        $statement->execute();
    } catch (Exception $e) {
        return false;
    } finally {
        disconnect_db($conn, $statement);
    }
}

function unlike_post(int $post_id, int $user_id)
{
    $has_liked = has_liked($post_id, $user_id);

    if (!$has_liked)
        return false;

    try {
        $conn = connect_db();

        $statement = $conn->prepare("DELETE FROM likes WHERE owner = ? AND post = ?");
        $statement->bind_param("ii", $user_id, $post_id);
        $statement->execute();
    } catch (Exception $e) {
        return false;
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
        $statement->bind_param("si", $new_name, $category_id);

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
        $statement->bind_param("is", $new_description, $category_id);

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

function set_topic_lock(int $topic_id, int $locked)
{
    try {
        if ($locked !== 1 && $locked !== 0)
            throw new Exception("Invalid lock state '$locked'.");

        $conn = connect_db();

        $statement = $conn->prepare("UPDATE topic SET locked = ? WHERE id = ?");
        $statement->bind_param("ii", $locked, $topic_id);

        if (!$statement->execute())
            throw new Exception("Failed to execute statement");

        return [
            "success" => true,
            "message" => "Topic updated successfully"
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

function delete_post(int $post_id)
{
    try {
        $conn = connect_db();

        $statement = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $statement->bind_param("i", $post_id);

        if (!$statement->execute())
            throw new Error("Failed to execute statement");

        return [
            "success" => true,
            "message" => "Post deleted successfully",
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

// CHECKS

function can_delete_post(array $post, array $topic, array $session)
{
    $is_admin = $session['admin'];
    $is_locked = $topic['topic']['locked'];

    if ($post['id'] === $topic['topic']['post_id'])
        return false;

    if ($is_admin)
        return true;

    if ($is_locked)
        return false;

    return $post['owner'] === $session['id'];
}