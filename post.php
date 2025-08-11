<?php
require_once __DIR__ . '/includes/db.php';
$id = intval($_GET['id']);

$post_result = mysqli_query($conn, "SELECT * FROM posts WHERE id=$id");
$post = mysqli_fetch_assoc($post_result);

if (!$post) {
    die("Post not found!");
}

$pageTitle = $post['title'];
include 'includes/header.php';
?>
<h1 class="mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
<p class="mb-4"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
<hr>
<h3 class="mt-4 mb-3">Comments</h3>
<div class="list-group mb-4">
<?php
$comments = mysqli_query($conn, "SELECT * FROM comments WHERE post_id=$id ORDER BY created_at DESC");
while ($comment = mysqli_fetch_assoc($comments)) {
    echo '<div class="list-group-item">';
    echo '<strong>' . htmlspecialchars($comment['name']) . ':</strong> ' . htmlspecialchars($comment['comment']);
    echo '</div>';
}
?>
</div>

<h3 class="mb-3">Leave a Comment</h3>
<form method="POST" class="row g-3">
    <div class="col-12 col-md-6">
        <input type="text" name="name" class="form-control" placeholder="Your Name" required>
    </div>
    <div class="col-12">
        <textarea name="comment" class="form-control" rows="4" placeholder="Your Comment" required></textarea>
    </div>
    <div class="col-12">
        <button type="submit" name="submit" class="btn btn-primary">Post Comment</button>
    </div>
</form>

<?php
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    mysqli_query($conn, "INSERT INTO comments (post_id, name, comment) VALUES ($id, '$name', '$comment')");
    header("Location: post.php?id=$id");
}
?>
<?php include 'includes/footer.php'; ?>