<?php

require_once("error.php");

function wip(string $what = "This feature")
{
    error_message("Not implemented!", "$what isn't impemented yet! This is probably being worked on as we speak. Come back later?", "index.php", "Home page");
    die;
}