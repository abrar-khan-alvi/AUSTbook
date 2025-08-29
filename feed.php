<?php
include 'header.php';
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $content]);
        header("Location: feed.php");
        exit;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- New Post Form Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="feed.php" method="POST">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" placeholder="What's on your mind, <?= htmlspecialchars($_SESSION['username']) ?>?" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>

        <!-- Feed Posts -->
        <h3 class="mb-3">Feed</h3>
        <?php
        $stmt = $pdo->query("
            SELECT posts.id, posts.content, posts.created_at, users.username
            FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC
        ");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
        ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-weight: bold;">
                            <?= htmlspecialchars(strtoupper(substr($row['username'], 0, 1))) ?>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?= htmlspecialchars($row['username']) ?></h5>
                            <small class="text-muted"><?= date('F j, Y, g:i a', strtotime($row['created_at'])) ?></small>
                        </div>
                    </div>
                    <p class="card-text mt-2"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                </div>

                <!-- Admin Controls Footer -->
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <div class="card-footer bg-light">
                    <a href="delete_post.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to permanently delete this post?');">Admin Delete</a>
                </div>
                <?php endif; ?>
                
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'footer.php'; ?>