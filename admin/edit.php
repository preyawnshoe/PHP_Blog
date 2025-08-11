<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
if ($id <= 0) {
    $pageTitle = 'Edit Post';
    include '../includes/header.php';
    echo "<div class='alert alert-danger'>Invalid post ID.</div>";
    echo "<p><a href='dashboard.php' class='btn btn-secondary mt-2'>Back to Dashboard</a></p>";
    include '../includes/footer.php';
    exit;
}

// Fetch existing post
$post = null;
if ($stmt = $conn->prepare('SELECT id, title, content FROM posts WHERE id = ?')) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
}

if (!$post) {
    $pageTitle = 'Edit Post';
    include '../includes/header.php';
    echo "<div class='alert alert-danger'>Post not found.</div>";
    echo "<p><a href='dashboard.php' class='btn btn-secondary mt-2'>Back to Dashboard</a></p>";
    include '../includes/footer.php';
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        $error = 'Both title and content are required.';
    } else {
        $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('ssi', $title, $content, $id);
            if ($stmt->execute()) {
                $success = 'Post updated successfully.';
                $post['title'] = $title;
                $post['content'] = $content;
            } else {
                $error = 'Failed to update post.';
            }
            $stmt->close();
        } else {
            $error = 'Database error.';
        }
    }
}

$pageTitle = 'Edit Post';
include '../includes/header.php';
?>
<h1 class="mb-4">Edit Post</h1>
<?php if ($success): ?><div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form method="POST" class="row g-3">
    <input type="hidden" name="id" value="<?php echo (int)$post['id']; ?>">
    <div class="col-12">
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" placeholder="Post Title" required>
    </div>
    <div class="col-12">
        <textarea name="content" class="form-control" rows="8" placeholder="Post Content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>
    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        <a href="delete.php?id=<?php echo (int)$post['id']; ?>" class="btn btn-outline-danger ms-auto">Delete Post</a>
    </div>
</form>
<?php include '../includes/footer.php'; ?>
