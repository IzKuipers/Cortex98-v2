<?php

require_once(__DIR__ . "/../../config.php");

$pages = [
    "home" => [
        "name" => "Home",
        "url" => WEB_ROOT . "/index.php",
        "color" => "rgb(255, 181, 162)"
    ],
    "links" => [
        "name" => "Links",
        "url" => WEB_ROOT . "/links.php",
        "color" => "rgb(148, 212, 255)"
    ],
    "files" => [
        "name" => "Files",
        "url" => WEB_ROOT . "/files.php",
        "color" => "rgb(157, 243, 114)"
    ],
    "forum" => [
        "name" => "Forum",
        "url" => WEB_ROOT . "/forum/index.php",
        "color" => "rgb(255, 230, 128)"
    ],
    "github" => [
        "name" => "GitHub Explorer",
        "url" => WEB_ROOT . "/github.php",
        "color" => "rgb(166, 242, 255)"
    ],
    "downloader" => [
        "name" => "Downloader",
        "url" => WEB_ROOT . "/downloader.php",
        "color" => "rgb(236, 195, 255)"
    ],
    "admin" => [
        "name" => "Admin",
        "url" => WEB_ROOT . "/admin/index.php",
        "color" => "rgb(255, 0, 0)",
        "hidden" => true
    ],
    "error" => [
        "name" => "Notice",
        "url" => WEB_ROOT . "/error.php",
        "color" => "rgb(247, 206, 24)",
        "hidden" => true
    ],
    "confirm" => [
        "name" => "Confirmation",
        "url" => WEB_ROOT . "/confirm.php",
        "color" => "rgb(247, 206, 24)",
        "hidden" => true
    ],
    "fs/newfolder" => [
        "name" => "New folder",
        "url" => WEB_ROOT . "/fs/newfolder.php",
        "color" => "rgb(157, 243, 114)",
        "hidden" => true
    ],
    "fs/upload" => [
        "name" => "Upload file",
        "url" => WEB_ROOT . "/fs/upload.php",
        "color" => "rgb(157, 243, 114)",
        "hidden" => true
    ],
];

function NavigationBar()
{
    global $pages;

    $links = "";
    $current_page = null;

    foreach ($pages as $id => $data) {
        $is_current = false;

        if (str_starts_with($_SERVER['REQUEST_URI'], WEB_ROOT . "/" . $id) || $_SERVER["REQUEST_URI"] === $data['url']) {
            $current_page = $data;
            $is_current = true;
        }

        $bg_color = $is_current ? "background-color: " . $data['color'] . ";" : "";
        $url = $data['url'];
        $name = $data['name'];

        if (!($data['hidden'] ?? false))
            $links .= <<<HTML
        <a style="$bg_color display: inline-block; padding: 0 5px; line-height: 16pt;" href="$url">$name</a> - 
        HTML;
    }

    $links = rtrim($links, " - ");

    if ($_SERVER['REQUEST_URI'] === WEB_ROOT . "/index.php") {
        $current_page = $pages["home"];
    }

    $left_td_style = "background-color: " . $current_page["color"] . "; display: inline-block; padding: 0 5px; line-height: 16pt;";
    $left_td_name = $current_page["name"];

    $left_td = ($current_page["hidden"] ?? false) ? <<<HTML
        <td>
            <a style="$left_td_style">$left_td_name</a>
        </td>
    HTML : "";

    $style = "border-bottom: " . $current_page["color"] . " 2px solid;";

    return <<<HTML
    <br>
    <table class="navigation" cellpadding="0" cellspacing="0" width="700" bgcolor="#dddddd" style="$style">
        <tr>
            $left_td
            <td align="right">
                $links
            </td>
        </tr>
    </table>
    <br>
    HTML;
}