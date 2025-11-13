<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../config/db.php';

// Redirect non-admin users
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

// Determine display name (use full_name, fall back to email)
$displayName = '';
if (!empty($_SESSION['user']['full_name'])) {
    $displayName = $_SESSION['user']['full_name'];
} elseif (!empty($_SESSION['user']['email'])) {
    $displayName = $_SESSION['user']['email'];
} else {
    $displayName = 'Admin';
}

// Get counts (safe queries)
$r = mysqli_query($conn, 'SELECT COUNT(*) AS cnt FROM books');
$books_count = $r ? intval(mysqli_fetch_assoc($r)['cnt']) : 0;

$r2 = mysqli_query($conn, 'SELECT COUNT(*) AS cnt FROM users');
$users_count = $r2 ? intval(mysqli_fetch_assoc($r2)['cnt']) : 0;

$r3 = mysqli_query($conn, 'SELECT COUNT(*) AS cnt FROM orders');
$orders_count = $r3 ? intval(mysqli_fetch_assoc($r3)['cnt']) : 0;
?>

<div class="dashboard-page">
  <section class="dashboard-header">
    <h1>📊 Admin Dashboard</h1>
    <p>Welcome back, <strong><?php echo htmlentities($displayName); ?></strong>! Here’s a quick overview of your store’s activity.</p>
  </section>

  <section class="dashboard-cards">
    <div class="dashboard-card">
      <div class="icon-box">📚</div>
      <h3><?php echo $books_count; ?></h3>
      <p>Total Books</p>
      <a href="/online_bookstore/admin/manage_books.php" class="btn-manage">Manage Books</a>
    </div>

    <div class="dashboard-card">
      <div class="icon-box">👥</div>
      <h3><?php echo $users_count; ?></h3>
      <p>Registered Users</p>
      <a href="/online_bookstore/admin/manage_users.php" class="btn-manage">Manage Users</a>
    </div>

    <div class="dashboard-card">
      <div class="icon-box">🛒</div>
      <h3><?php echo $orders_count; ?></h3>
      <p>Total Orders</p>
      <a href="/online_bookstore/admin/manage_orders.php" class="btn-manage">Manage Orders</a>
    </div>
  </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
