<?php

const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "c98";
const FS_MAX_SIZE = 1073741824; // 1GB

const C98_DISABLE_REGISTRATION = false;
const C98_LOCKDOWN = false
    // Leave the below OR expression in place. Removing it may have unforseen consequences. Thank you.

    || C98_DISABLE_REGISTRATION;

const WEB_ROOT = "/app"; // The root of Cortex 98. Empty string means root (so e.g. /index.php)