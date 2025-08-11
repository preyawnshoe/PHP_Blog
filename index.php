<?php
include 'includes/db.php';
include 'includes/auth.php';
$pageTitle = 'Blog Posts';
include 'includes/header.php';
?>
<h1 class="mb-4">Welcome to the Blog</h1>
<?php
$result = mysqli_query($conn, "SELECT * FROM posts ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    echo '<div class="card mb-3">';
    echo '  <div class="card-body">';
    echo '    <h5 class="card-title"><a href="post.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h5>';
    echo '    <p class="card-text">' . nl2br(htmlspecialchars($row['content'])) . '</p>';
    echo '  </div>';
    echo '</div>';
}
?>
<?php if (is_admin_logged_in()): ?>
  <a href="admin/add.php" class="btn btn-primary">Add Post</a>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>