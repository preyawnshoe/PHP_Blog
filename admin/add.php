<?php include '../includes/db.php'; ?>
<?php include '../includes/auth.php'; require_admin(); ?>
<?php $pageTitle = 'Add Post'; include '../includes/header.php'; ?>
<h1 class="mb-4">Add New Post</h1>
<form method="POST" class="row g-3">
    <div class="col-12">
        <input type="text" name="title" class="form-control" placeholder="Post Title" required>
    </div>
    <div class="col-12">
        <textarea name="content" class="form-control" rows="6" placeholder="Post Content" required></textarea>
    </div>
    <div class="col-12">
        <button type="submit" name="submit" class="btn btn-primary">Publish</button>
    </div>
</form>

<?php
if (isset($_POST['submit'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        echo "<div class='alert alert-danger mt-3' role='alert'>Both title and content are required.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param('ss', $title, $content);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success mt-3' role='alert'>Post added!</div>";
                echo "<p class='mt-2'><a class='btn btn-link' href='dashboard.php'>Go to Dashboard</a></p>";
            } else {
                echo "<div class='alert alert-danger mt-3' role='alert'>Failed to add post.</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger mt-3' role='alert'>Database error.</div>";
        }
    }
}
?>
<?php include '../includes/footer.php'; ?>