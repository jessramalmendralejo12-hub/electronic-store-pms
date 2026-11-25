<?php
/*
 * Database connection.
 * - On XAMPP (localhost) it uses the default XAMPP settings.
 * - On InfinityFree it uses the credentials you copy from the
 *   InfinityFree Control Panel -> MySQL Databases.
 */

$serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
$isLocal    = in_array($serverName, ['localhost', '127.0.0.1'], true);

if ($isLocal) {
    // ---- XAMPP (local) ----
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'electronic_store_db';
} else {
    // ---- InfinityFree (live) : REPLACE these 4 values ----
    $db_host = 'sqlXXX.infinityfree.com';          // MySQL Hostname
    $db_user = 'if0_XXXXXXXX';                     // MySQL Username
    $db_pass = 'YOUR_DB_PASSWORD';                 // MySQL Password
    $db_name = 'if0_XXXXXXXX_electronic_store_db'; // Full database name
}

// Make mysqli throw exceptions so we can show a friendly message
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    die('<h3>Database connection failed.</h3><p>Check your settings in <code>config/db_connect.php</code>.</p>');
}
