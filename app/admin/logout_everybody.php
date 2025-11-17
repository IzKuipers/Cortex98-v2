<?php

require_once(__DIR__ . "/../lib/session.php");
require_once(__DIR__ . "/../lib/user.php");

verify_loggedin(true);
$session = get_user_from_session();

if (isset($_GET["confirm"])) {


    delete_all_tokens_except($session['id']);

    header("location: index.php");
} else {
    confirm_message(
        "Log out everybody?",
        "Are you SURE you want to delete all active tokens of all users except yourself? This is a potentially destructive action.",
        "admin/index.php",
        "admin/logout_everybody.php?confirm=1",
        "ABORT!",
        "Yes, I'm sure"
    );
}
