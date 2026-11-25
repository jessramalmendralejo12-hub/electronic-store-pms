<?php
// Loaded at the top of every page: session + database + helper functions.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/functions.php';
