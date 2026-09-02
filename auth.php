<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function users_file(): string {
    $primary = __DIR__ . '/data/users.json';
    if (is_writable($primary) || (!file_exists($primary) && is_writable(__DIR__ . '/data'))) {
        return $primary;
    }
    $tmp_dir = sys_get_temp_dir() . '/skyport_data';
    if (!is_dir($tmp_dir)) {
        @mkdir($tmp_dir, 0777, true);
    }
    $tmp_file = $tmp_dir . '/users.json';
    if (!file_exists($tmp_file) && file_exists($primary)) {
        @copy($primary, $tmp_file);
    }
    return file_exists($tmp_file) ? $tmp_file : $primary;
}

function all_users(): array {
    $file = users_file();
    if (!file_exists($file)) return [];
    $users = json_decode((string) file_get_contents($file), true);
    return is_array($users) ? $users : [];
}

function save_users(array $users): void {
    $file = users_file();
    $directory = dirname($file);
    if (!is_dir($directory)) @mkdir($directory, 0775, true);
    @file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
}

function find_user(string $email): ?array {
    foreach (all_users() as $user) {
        if (strcasecmp($user['email'] ?? '', trim($email)) === 0) return $user;
    }
    return null;
}

function sign_in_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
}
?>
