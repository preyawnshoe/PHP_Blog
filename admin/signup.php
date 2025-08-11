<?php
include '../includes/db.php';
include '../includes/auth.php';

$error = '';
$success = '';

// Create the admins table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Check if signup is allowed (you can add additional restrictions here)
$allowSignup = true; // Set to false if you want to restrict signup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (!$allowSignup) {
        $error = 'Admin signup is currently disabled.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($username) < USERNAME_MIN_LENGTH) {
        $error = 'Username must be at least ' . USERNAME_MIN_LENGTH . ' characters long.';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare('SELECT id FROM admins WHERE username = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = 'Username already exists. Please choose a different username.';
            } else {
                // Create new admin account
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $insertStmt = $conn->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
                
                if ($insertStmt) {
                    $insertStmt->bind_param('ss', $username, $password_hash);
                    if ($insertStmt->execute()) {
                        $success = 'Admin account created successfully! You can now <a href="login.php">login</a>.';
                        // Clear form data
                        $_POST = [];
                    } else {
                        $error = 'Failed to create admin account. Please try again.';
                    }
                    $insertStmt->close();
                } else {
                    $error = 'Database error occurred.';
                }
            }
            $stmt->close();
        } else {
            $error = 'Database error occurred.';
        }
    }
}

$pageTitle = 'Admin Signup';
include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Admin Signup</h3>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$success): ?>
                        <form method="POST" action="signup.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                       required minlength="<?php echo USERNAME_MIN_LENGTH; ?>">
                                <div class="form-text">Username must be at least <?php echo USERNAME_MIN_LENGTH; ?> characters long.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       required minlength="<?php echo PASSWORD_MIN_LENGTH; ?>">
                                <div class="form-text">Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm" name="confirm" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Create Admin Account</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <div class="mt-3 text-center">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                        <p><a href="../index.php" class="text-muted">Back to blog</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
