<?php

const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "c98";
const FS_MAX_SIZE = 1073741824; // 1GB

const SESSION_LIFETIME = 0;
const SESSION_PATH = "/";
const SESSION_DOMAIN = null;
const SESSION_SECURE = 1;
const SESSION_HTTPONLY = 1;

const C98_LOCKDOWN = false;
const C98_DISABLE_REGISTRATION = false
    // Leave the below OR expression in place. Removing it may have unforseen consequences. Thank you.

    || C98_LOCKDOWN;

const WEB_ROOT = "/app"; // The root of Cortex 98. Empty string means root (so e.g. /index.php)