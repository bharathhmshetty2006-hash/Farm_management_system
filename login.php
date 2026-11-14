<?php
require_once __DIR__ . '/config/db.php';

// If already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Check password using password_verify() or default fallback 'admin123'
        if ($user && (password_verify($password, $user['password']) || $password === 'admin123')) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            set_flash('success', 'Welcome back, ' . $user['full_name'] . '!');
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farm Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="card login-card p-4 shadow-lg border-0">
        <div class="card-body text-center">
            <div class="mb-3">
                <i class="fa-solid fa-wheat-awn text-success display-4"></i>
            </div>
            <h3 class="fw-bold text-success mb-1">Farm Management</h3>
            <p class="text-muted small mb-4">VTU 5th Sem Mini Project Dashboard</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small text-start" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="text-start">
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="admin" required value="admin">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required value="admin123">
                    </div>
                    <div class="form-text text-muted small">Default credentials: <code>admin</code> / <code>admin123</code></div>
                </div>

                <button type="submit" class="btn btn-farm w-100 py-2 fw-semibold">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center text-muted small">
                Dept of Computer Science & Engineering<br>
                <strong>Govt Engineering College, Hassan</strong>
            </div>
        </div>
    </div>
</body>
</html>
