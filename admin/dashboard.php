<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();
$pageTitle = 'Admin Dashboard';
include '../includes/header.php';
?>
<h1 class="mb-4">Admin Dashboard</h1>
<p class="mb-3">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? ''); ?>.</p>
<ul class="list-group mb-4">
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>Add a new post</span>
        <a href="add.php" class="btn btn-primary btn-sm">Add Post</a>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>End your session</span>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </li>
</ul>

<h2 class="h4 mb-3">All Posts</h2>
<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th style="width: 60px;">ID</th>
        <th>Title</th>
        <th style="width: 200px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $res = $conn->query('SELECT id, title FROM posts ORDER BY id ASC');
      if ($res) {
          while ($row = $res->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . (int)$row['id'] . '</td>';
              echo '<td>' . htmlspecialchars($row['title']) . '</td>';
              echo '<td>';
              echo '  <a href="edit.php?id=' . (int)$row['id'] . '" class="btn btn-sm btn-outline-primary me-2">Edit</a>';
              echo '  <a href="delete.php?id=' . (int)$row['id'] . '" class="btn btn-sm btn-outline-danger">Delete</a>';
              echo '</td>';
              echo '</tr>';
          }
      } else {
          echo '<tr><td colspan="3" class="text-muted">No posts found.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?>
