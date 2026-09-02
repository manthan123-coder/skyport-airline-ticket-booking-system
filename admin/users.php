<?php
require_once __DIR__ . '/../auth.php';

$message = '';
$message_type = 'success';

$users = all_users();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_user') {
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'User');

        if (!empty($name) && !empty($email) && !empty($password)) {
            $existing = find_user($email);
            if ($existing) {
                $message = "User with email <strong>{$email}</strong> already exists.";
                $message_type = 'danger';
            } else {
                $new_user = [
                    'id' => uniqid('usr_'),
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $users[] = $new_user;
                save_users($users);
                $message = "User account for <strong>{$name}</strong> created successfully!";
                $message_type = 'success';
            }
        }
    }

    if ($action === 'delete_user') {
        $email_to_delete = strtolower(trim($_POST['email'] ?? ''));
        $updated_users = [];
        foreach ($users as $u) {
            if (strcasecmp($u['email'] ?? '', $email_to_delete) === 0) {
                continue;
            }
            $updated_users[] = $u;
        }
        $users = $updated_users;
        save_users($users);
        $message = "User account removed successfully.";
        $message_type = 'warning';
    }
}

include 'header.php';
include 'sidebar.php';
?>

<!-- Main Content Area -->
<main class="app-main">

    <!-- Header Title -->
    <div class="app-content-header py-3 bg-body-secondary border-bottom mb-4">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i>Manage Users & Staff</h3>
                    <small class="text-muted">Registered Passenger & Admin Accounts</small>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <button class="btn btn-primary rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus me-1"></i> Add Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid px-4">

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i><?= $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- USERS LIST CARD -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-person-badge text-primary me-2"></i>System User Accounts (<?= count($users); ?> Accounts)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User Name</th>
                                    <th>Email Address</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No registered users found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $u): 
                                        $uname = htmlspecialchars($u['name'] ?? 'User');
                                        $uemail = htmlspecialchars($u['email'] ?? '');
                                        $urole = htmlspecialchars($u['role'] ?? 'User');
                                        $ucreated = htmlspecialchars($u['created_at'] ?? 'N/A');
                                        $badge_class = ($urole === 'Admin') ? 'bg-danger' : 'bg-primary';
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                                        <?= strtoupper(substr($uname, 0, 1)); ?>
                                                    </div>
                                                    <span class="fw-bold text-dark"><?= $uname; ?></span>
                                                </div>
                                            </td>
                                            <td><span class="text-muted"><i class="bi bi-envelope me-1"></i><?= $uemail; ?></span></td>
                                            <td><span class="badge <?= $badge_class; ?> rounded-pill px-3 py-1"><?= $urole; ?></span></td>
                                            <td class="small text-muted"><?= $ucreated; ?></td>
                                            <td class="text-end">
                                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete user <?= $uemail; ?>?');">
                                                    <input type="hidden" name="action" value="delete_user">
                                                    <input type="hidden" name="email" value="<?= $uemail; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Add New User Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_user">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Account Role</label>
                        <select name="role" class="form-select" required>
                            <option value="User">Passenger / Customer</option>
                            <option value="Admin">System Administrator</option>
                            <option value="Staff">Airline Staff</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4"><i class="bi bi-check-circle me-1"></i> Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
