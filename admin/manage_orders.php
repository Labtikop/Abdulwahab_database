<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../config/db.php';

// Restrict non-admin users
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
  header('Location: ../pages/login.php');
  exit;
}

// Update order status
if (isset($_POST['update_status'])) {
  $oid = intval($_POST['order_id']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);
  mysqli_query($conn, "UPDATE orders SET status='{$status}' WHERE order_id={$oid}");
  echo '<div class="alert alert-success text-center">✅ Order status updated successfully.</div>';
}

// Fetch all orders
$res = mysqli_query($conn, 'SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC');
?>

<div class="admin-page">
  <section class="admin-header">
    <h1>📦 Manage Orders</h1>
    <p>View, update, and track all customer orders efficiently.</p>
  </section>

  <section class="admin-table-section">
    <div class="card table-card p-3">
      <h5 class="mb-3 text-accent">All Orders</h5>
      <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Total</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($o = mysqli_fetch_assoc($res)) : ?>
              <tr>
                <td><?php echo $o['order_id']; ?></td>
                <td><?php echo htmlentities($o['full_name']); ?></td>
                <td>₱<?php echo number_format($o['total_amount'], 2); ?></td>
                <td><span class="status-badge <?php echo strtolower($o['status']); ?>"><?php echo htmlentities($o['status']); ?></span></td>
                <td><a class="btn btn-sm btn-accent" href="?view=<?php echo $o['order_id']; ?>">View</a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <?php if (isset($_GET['view'])) :
    $oid = intval($_GET['view']);
    $r = mysqli_query($conn, "SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_id = {$oid} LIMIT 1");
    $ord = mysqli_fetch_assoc($r);
  ?>
    <section class="order-details mt-4">
      <div class="card p-4">
        <h4 class="text-accent">Order #<?php echo $ord['order_id']; ?></h4>
        <p><strong>Customer:</strong> <?php echo htmlentities($ord['full_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlentities($ord['email']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlentities($ord['status']); ?></p>

        <h5 class="mt-3">🛒 Items</h5>
        <ul class="order-items">
          <?php
          $itres = mysqli_query($conn, "SELECT oi.*, b.title FROM order_items oi JOIN books b ON oi.book_id = b.book_id WHERE oi.order_id = {$oid}");
          while ($it = mysqli_fetch_assoc($itres)) {
            echo '<li>' . htmlentities($it['title']) . ' x ' . intval($it['quantity']) . ' — ₱' . number_format($it['price'] * $it['quantity'], 2) . '</li>';
          }
          ?>
        </ul>

        <form method="POST" class="mt-3">
          <input type="hidden" name="order_id" value="<?php echo $ord['order_id']; ?>">
          <select name="status" class="form-select mb-2">
            <option>Processing</option>
            <option>Shipped</option>
            <option>Delivered</option>
            <option>Cancelled</option>
          </select>
          <button class="btn btn-accent" name="update_status" type="submit">Update Status</button>
        </form>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
