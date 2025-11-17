<?php

require_once(__DIR__ . "/lib/error.php");
require_once(__DIR__ . "/lib/session.php");
require_once(__DIR__ . "/lib/links.php");

verify_loggedin();

if (!isset($_GET["linkid"]))
    invalid_link();

click_link($_GET["linkid"]);