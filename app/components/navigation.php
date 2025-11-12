<?php

function NavigationBar()
{
    return <<<HTML
    <br>
    <table class="navigation" cellpadding="2" cellspacing="2" width="700" bgcolor="#dddddd">
        <tr>
            <td align="right">
                <a href="index.php">Home</a> -
                <a href="links.php">Links</a> -
                <a href="files.php">Files</a> -
                <a href="github.php">GitHub Explorer</a> -
                <a href="downloader.php">Downloader</a>
            </td>
        </tr>
    </table>
    <br>
    HTML;
}