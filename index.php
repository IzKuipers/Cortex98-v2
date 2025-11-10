<?php

/*

ABOUT THIS FILE

This file is just a bridge between the root of the webserver and the app/ folder.
I'm only doing this to clearly separate the source files of the application from other
stuff outside of the app folder, like README.md.

*/

header("location: ./app/index.php");