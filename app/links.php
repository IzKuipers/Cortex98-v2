<?php
require_once("lib/session.php");
require_once("lib/links.php");

verify_loggedin();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["name"], $_POST["url"])) {
    $description = isset($_POST["description"]) ? $_POST["description"] : "";

    $result = create_link_as_session(htmlspecialchars($_POST["name"]), htmlspecialchars($_POST["url"]), htmlspecialchars($description));

    if (!$result) {
        error_message("Couldn't add link", "There might already be a link with that name, or the link is invalid. Please try something else.", "links.php");
    }
}

$links = get_all_links();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="POST">
        <table>
            <tr>
                <td>Name</td>
                <td>
                    <input type="text" name="name">
                </td>
            </tr>
            <tr>
                <td>URL</td>
                <td>
                    <input type="text" name="url">
                </td>
            </tr>
            <tr>
                <td>Description?</td>
                <td>
                    <input type="text" name="description">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    <button type="submit">Create</button>
                </td>
            </tr>
        </table>
    </form>

    <table>

        <?php foreach ($links as $link): ?>
            <tr>
                <td>
                    <?= $link["name"] ?>
                </td>
                <td>
                    <?= $link["target"] ?>
                </td>
                <td>
                    <?= $link["visits"] ?>
                </td>
                <td>
                    <?= $link["description"] ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</body>

</html>