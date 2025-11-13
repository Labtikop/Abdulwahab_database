<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    echo '<div class="orders-page"><div class="orders-card"><p>Please <a href="/online_bookstore/pages/login.php" class="link">login</a> to view your orders.</p></div></div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$uid = intval($_SESSION['user']['user_id']);
$stmt = mysqli_prepare($conn, 'SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC');
mysqli_stmt_bind_param($stmt, 'i', $uid);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<div class="orders-page">
  <div class="orders-container">
    <h2>My Orders</h2>

    <?php if (!$res || mysqli_num_rows($res) === 0): ?>
      <div class="orders-card">
        <p>You have no orders yet.</p>
      </div>
    <?php else: ?>
      <?php while ($o = mysqli_fetch_assoc($res)): ?>
        <div class="orders-card">
          <div class="orders-header">
            <div>
              <strong>Order #<?php echo $o['order_id']; ?></strong>
            </div>
            <div class="order-date">
              <?php echo date('F d, Y h:i A', strtotime($o['order_date'])); ?>
            </div>
            <div class="order-status status-<?php echo strtolower($o['status']); ?>">
              <?php echo htmlentities($o['status']); ?>
            </div>
          </div>

          <hr>

          <?php
            $stmt2 = mysqli_prepare($conn, 'SELECT oi.quantity, oi.price, b.title FROM order_items oi JOIN books b ON oi.book_id=b.book_id WHERE oi.order_id=?');
            mysqli_stmt_bind_param($stmt2, 'i', $o['order_id']);
            mysqli_stmt_execute($stmt2);
            $items = mysqli_stmt_get_result($stmt2);
          ?>

          <ul class="order-items">
            <?php while ($it = mysqli_fetch_assoc($items)): ?>
              <li>
                <span class="book-title"><?php echo htmlentities($it['title']); ?></span>
                <span class="book-details">× <?php echo intval($it['quantity']); ?> — ₱<?php echo number_format($it['price'] * $it['quantity'], 2); ?></span>
              </li>
            <?php endwhile; ?>
          </ul>

          <p class="order-total">
            <strong>Total: ₱<?php echo number_format($o['total_amount'], 2); ?></strong>
          </p>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
