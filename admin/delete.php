<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
if ($id <= 0) {
    $pageTitle = 'Delete Post';
    include '../includes/header.php';
    echo "<div class='alert alert-danger'>Invalid post ID.</div>";
    echo "<p><a href='dashboard.php' class='btn btn-secondary mt-2'>Back to Dashboard</a></p>";
    include '../includes/footer.php';
    exit;
}

// Fetch title for confirmation
$title = '';
if ($stmt = $conn->prepare('SELECT title FROM posts WHERE id = ?')) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title);
    $stmt->fetch();
    $stmt->close();
}

if ($title === '') {
    $pageTitle = 'Delete Post';
    include '../includes/header.php';
    echo "<div class='alert alert-danger'>Post not found.</div>";
    echo "<p><a href='dashboard.php' class='btn btn-secondary mt-2'>Back to Dashboard</a></p>";
    include '../includes/footer.php';
    exit;
}

// Simple CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $pageTitle = 'Delete Post';
        include '../includes/header.php';
        echo "<div class='alert alert-danger'>Invalid form token. Please try again.</div>";
        echo "<p><a href='dashboard.php' class='btn btn-secondary mt-2'>Back to Dashboard</a></p>";
        include '../includes/footer.php';
        exit;
    }
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        if ($stmt = $conn->prepare('DELETE FROM posts WHERE id = ?')) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit;
            }
            $stmt->close();
        }
        $pageTitle = 'Delete Post';
        include '../includes/header.php';
        echo "<div class='alert alert-danger'>Failed to delete post.</div>";
        echo "<p><a href='dashboard.php' class='btn btn-secondary mt-2'>Back to Dashboard</a></p>";
        include '../includes/footer.php';
        exit;
    } else {
        header('Location: edit.php?id=' . $id);
        exit;
    }
}

$pageTitle = 'Delete Post';
include '../includes/header.php';
?>
<h1 class="mb-4">Delete Post</h1>
<div class="alert alert-warning" role="alert">
  Are you sure you want to delete the post: <strong><?php echo htmlspecialchars($title); ?></strong>?
</div>
<form method="POST" class="d-flex gap-2">
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
  <button type="submit" name="confirm" value="yes" class="btn btn-danger">Yes, delete</button>
  <a href="edit.php?id=<?php echo (int)$id; ?>" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>
