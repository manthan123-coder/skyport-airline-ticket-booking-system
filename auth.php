<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function users_file(): string {
    return __DIR__ . '/data/users.json';
}

function all_users(): array {
    $file = users_file();
    if (!file_exists($file)) return [];
    $users = json_decode((string) file_get_contents($file), true);
    return is_array($users) ? $users : [];
}

function save_users(array $users): void {
    $directory = dirname(users_file());
    if (!is_dir($directory)) mkdir($directory, 0775, true);
    file_put_contents(users_file(), json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
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
