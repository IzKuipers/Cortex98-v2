<?php

require_once(__DIR__ . "/../lib/error.php");
require_once(__DIR__ . "/../lib/session.php");

verify_loggedin();

confirm_message("Log out?","Are you sure you want to log out?","index.php","account/logoutconfirm.php","Stay","Log out");