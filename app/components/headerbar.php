<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../../config.php");

function HeaderBar(bool $offline = false)
{
    $session = !$offline ? get_user_from_session() : null;
    $username = $session ? $session["username"] : "Stranger";
    $web_root = WEB_ROOT;

    $links = $session ? <<<HTML
     - <a href="$web_root/account/logout.php">Log out</a>
     - <a href="$web_root/account/account.php">Account</a>
    HTML : "";

    $admin_tag = ($session && $session["admin"]) ? <<<HTML
    <a style="background-color: #ff0000; text-decoration: none; color: #ffffff;" href="$web_root/admin/index.php">&nbsp;<b>ADMIN</b>&nbsp;</a>
    HTML : "";

    $result = <<<HTML
    <table border="0" cellpadding="8" cellspacing="0" width="600" bgcolor="#ffffff">
        <tr>
            <td width="25%">
                    <img src="$web_root/assets/c98banner.gif" alt="" height="32" width="250" style="vertical-align: middle;">
            </td>
            <td align="right" nowrap>
                $admin_tag
                <span>Welcome, <b>$username</b></span>
                $links
            </td>
        </tr>
    </table>
    HTML;

    if (C98_LOCKDOWN) {
        $result .= <<<HTML
        <br>
        <table border="0" cellpadding="2" cellspacing="4" width="600" bgcolor="#ff0000">
            <td>
                <img src="$web_root/assets/symbols/warning.gif" alt="">
                <font color="#ffffff"><b>Cortex 98 is in lockdown! Only administrators can access the web site.</b></font>
            </td>
        </table>
        HTML;
    }

    return $result;
}