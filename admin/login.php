<?php
include '../includes/db.php';
include '../includes/auth.php';

$error = '';
$info = '';

$conn->query("CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$adminsCount = 0;
if ($result = $conn->query('SELECT COUNT(*) AS c FROM admins')) {
    $row = $result->fetch_assoc();
    $adminsCount = (int)$row['c'];
    $result->free();
}

function get_safe_redirect(string $fallback = 'dashboard.php'): string {
    $redirect = $_GET['redirect'] ?? $fallback;
    if (!is_string($redirect)) {
        return $fallback;
    }
    return $redirect;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create_admin' && $adminsCount === 0) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (strlen($username) < 3 || strlen($password) < 6) {
            $error = 'Choose a longer username (>=3) and password (>=6).';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
            if ($stmt) {
                $stmt->bind_param('ss', $username, $hash);
                if ($stmt->execute()) {
                    $info = 'Admin account created. Please log in.';
                } else {
                    $error = 'Could not create admin (maybe username already exists).';
                }
                $stmt->close();
            } else {
                $error = 'Database error.';
            }
            if ($result = $conn->query('SELECT COUNT(*) AS c FROM admins')) {
                $row = $result->fetch_assoc();
                $adminsCount = (int)$row['c'];
                $result->free();
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
        } else {
            $stmt = $conn->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
            if ($stmt) {
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->bind_result($id, $uname, $password_hash);
                if ($stmt->fetch()) {
                    if (password_verify($password, $password_hash)) {
                        login_admin((int)$id, $uname);
                        header('Location: ' . get_safe_redirect());
                        exit;
                    } else {
                        $error = 'Invalid credentials.';
                    }
                } else {
                    $error = 'Invalid credentials.';
                }
                $stmt->close();
            } else {
                $error = 'Database error.';
            }
        }
    }
}

$pageTitle = 'Admin Login';
include '../includes/header.php';
?>
<h1 class="mb-4">Admin</h1>
<?php if ($info): ?>
<div class="alert alert-success" role="alert"><?php echo htmlspecialchars($info); ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($adminsCount === 0): ?>
<div class="card mb-4">
  <div class="card-header">Create Admin Account</div>
  <div class="card-body">
    <form method="post" class="row g-3">
      <input type="hidden" name="action" value="create_admin">
      <div class="col-12">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="col-md-6">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="col-md-6">
        <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">Create Admin</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header">Login</div>
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="col-12">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-success">Login</button>
      </div>
    </form>
    
    <div class="mt-3 text-center">
      <p>Don't have an admin account? <a href="signup.php">Sign up here</a></p>
    </div>
  </div>
</div>

<p class="mt-3"><a href="../index.php" class="link-secondary">Back to blog</a></p>
<?php include '../includes/footer.php'; ?> 