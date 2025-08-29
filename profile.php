<?php
include 'header.php';
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $post_id = filter_var($_POST['update_id'], FILTER_VALIDATE_INT);
    $content = trim($_POST['content']);
    if ($post_id && !empty($content)) {
        $stmt = $pdo->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$content, $post_id, $user_id]);
    }
    header("Location: profile.php");
    exit;
}

if (isset($_GET['delete'])) {
    $post_id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($post_id) {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
    }
    header("Location: profile.php");
    exit;
}

$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$stmt_count->execute([$user_id]);
$post_count = $stmt_count->fetchColumn();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">My Profile</h2>

        <!-- User Information Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px; font-size: 2.5rem; font-weight: bold;">
                        <?= htmlspecialchars(strtoupper(substr($_SESSION['username'], 0, 1))) ?>
                    </div>
                    <div>
                        <h3 class="card-title mb-0"><?= htmlspecialchars($_SESSION['username']) ?></h3>
                        <p class="card-text text-muted">AUSTbook Member</p>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col">
                        <h5>Posts</h5>
                        <p class="h4"><?= $post_count ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Post Form -->
        <?php if (isset($_GET['edit'])):
            $edit_id = filter_var($_GET['edit'], FILTER_VALIDATE_INT);
            $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
            $stmt->execute([$edit_id, $user_id]);
            $post_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($post_to_edit):
        ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Edit Post</h5>
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="update_id" value="<?= htmlspecialchars($post_to_edit['id']) ?>">
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="3" required><?= htmlspecialchars($post_to_edit['content']) ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="profile.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        <?php 
            endif;
        endif;
        ?>

        <!-- User's Posts -->
        <h3 class="mb-3">My Posts</h3>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
        ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                         <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-weight: bold;">
                            <?= htmlspecialchars(strtoupper(substr($_SESSION['username'], 0, 1))) ?>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?= htmlspecialchars($_SESSION['username']) ?></h5>
                            <small class="text-muted"><?= date('F j, Y, g:i a', strtotime($row['created_at'])) ?></small>
                        </div>
                    </div>
                    <p class="card-text mt-2"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                </div>
                <div class="card-footer bg-white">
                    <a href="profile.php?edit=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
                    <a href="profile.php?delete=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'footer.php'; ?>