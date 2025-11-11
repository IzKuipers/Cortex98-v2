<?php

require_once(__DIR__ . "/../lib/session.php");

function HeaderBar()
{
    $session = get_user_from_session();

    $username = $session ? $session["username"] : "Stranger";

    $links = $session ? <<<HTML
     - <a href="logout.php">Log out</a>
     - <a href="account.php">Account</a>
    HTML : "";

    return <<<HTML
    <table border="0" cellpadding="2" cellspacing="0" width="600">
        <tr>
            <td width="25%">
                    <img src="assets/c98banner.gif" alt="" height="32" width="250">
            </td>
            <td align="right" nowrap>
                <span>Welcome, <b>$username</b></span>
                $links
            </td>
        </tr>
    </table>
    HTML;
}