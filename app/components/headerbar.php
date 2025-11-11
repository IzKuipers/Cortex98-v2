<?php

require_once(__DIR__ . "/../lib/session.php");

function HeaderBar()
{
    $session = get_user_from_session();

    $username = $session ? $session["username"] : "Stranger";

    $links = $session ? <<<HTML
     - <a href="account/logout.php">Log out</a>
     - <a href="account/account.php">Account</a>
    HTML : "";

    return <<<HTML
    <table border="0" cellpadding="8" cellspacing="0" width="600" bgcolor="#ffffff">
        <tr>
            <td width="25%">
                    <img src="assets/c98banner.gif" alt="" height="32" width="250" style="vertical-align: middle;">
            </td>
            <td align="right" nowrap>
                <span>Welcome, <b>$username</b></span>
                $links
            </td>
        </tr>
    </table>
    HTML;
}