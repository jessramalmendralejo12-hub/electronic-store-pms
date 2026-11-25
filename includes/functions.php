<?php
// ------------------------------------------------------------
// Settings
// ------------------------------------------------------------
define('LOW_STOCK_LIMIT', 10);                  // stock <= this is "Low Stock"
define('UPLOAD_DIR', __DIR__ . '/../uploads/'); // product image folder

// BASE_URL is detected automatically (works in a XAMPP subfolder and in InfinityFree's htdocs).
$__root = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
$__doc  = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
define('BASE_URL', ($__doc !== '' && strpos($__root, $__doc) === 0) ? substr($__root, strlen($__doc)) : '');

// ------------------------------------------------------------
// Small helpers
// ------------------------------------------------------------
function e($value): string            // escape output (prevents XSS)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function money($amount): string
{
    return '₱' . number_format((float) $amount, 2);
}

// Returns [label, bootstrap-color] for a stock quantity
function stock_status(int $qty): array
{
    if ($qty <= 0) {
        return ['Out of Stock', 'danger'];
    }
    if ($qty <= LOW_STOCK_LIMIT) {
        return ['Low Stock', 'warning'];
    }
    return ['In Stock', 'success'];
}

// ------------------------------------------------------------
// Flash messages (success / error notifications)
// ------------------------------------------------------------
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

// ------------------------------------------------------------
// Authentication
// ------------------------------------------------------------
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

// ------------------------------------------------------------
// CSRF protection (for forms that change data)
// ------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', (string) $sent)) {
        set_flash('danger', 'Invalid or expired request. Please try again.');
        redirect('dashboard.php');
    }
}

// ------------------------------------------------------------
// Database helpers (all use prepared statements)
// ------------------------------------------------------------
function db_select(mysqli $conn, string $sql, string $types = '', array $params = []): array
{
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function db_one(mysqli $conn, string $sql, string $types = '', array $params = []): ?array
{
    $rows = db_select($conn, $sql, $types, $params);
    return $rows[0] ?? null;
}

function db_value(mysqli $conn, string $sql, string $types = '', array $params = [])
{
    $row = db_one($conn, $sql, $types, $params);
    return $row ? array_values($row)[0] : null;
}

// For INSERT / UPDATE / DELETE. Returns number of affected rows.
function db_execute(mysqli $conn, string $sql, string $types = '', array $params = []): int
{
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    return $affected;
}

// ------------------------------------------------------------
// Product image upload
// ------------------------------------------------------------
function upload_image(array $file, array &$errors): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null; // no image chosen (this is allowed)
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Image upload failed. Please try again.';
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $errors[] = 'Image must be 2MB or smaller.';
        return null;
    }
    $info    = @getimagesize($file['tmp_name']);
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($allowed[$info[2]])) {
        $errors[] = 'Image must be a JPG, PNG, GIF or WEBP file.';
        return null;
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) {
        $errors[] = 'Could not save the image. Check the uploads/ folder permissions.';
        return null;
    }
    return $name;
}

function delete_image(?string $name): void
{
    if ($name) {
        $path = UPLOAD_DIR . basename($name);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
