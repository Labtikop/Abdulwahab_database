<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    echo '<div class="checkout-page"><div class="checkout-card"><p>Please <a href="/online_bookstore/pages/login.php" class="link">login</a> to complete checkout.</p></div></div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

if (empty($_SESSION['cart'])) {
    echo '<div class="checkout-page"><div class="checkout-card"><p>Your cart is empty.</p></div></div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$items = [];
$total = 0;

foreach ($_SESSION['cart'] as $bid => $qty) {
    $stmt = mysqli_prepare($conn, 'SELECT book_id, title, price, stock FROM books WHERE book_id=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $bid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $b = mysqli_fetch_assoc($res);
    if (!$b) continue;

    $items[] = [
        'book_id' => $b['book_id'],
        'title' => $b['title'],
        'price' => $b['price'],
        'qty' => $qty,
        'stock' => $b['stock']
    ];
    $total += $b['price'] * $qty;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $uid = intval($_SESSION['user']['user_id']);
    if ($uid <= 0) {
        $error = 'Invalid user session. Please log in again.';
    } else {
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, 'INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, "Processing")');
            mysqli_stmt_bind_param($stmt, 'id', $uid, $total);
            mysqli_stmt_execute($stmt);
            $oid = mysqli_insert_id($conn);

            foreach ($items as $it) {
                if ($it['qty'] > $it['stock']) throw new Exception('Not enough stock for ' . $it['title']);
                $stmt2 = mysqli_prepare($conn, 'INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)');
                mysqli_stmt_bind_param($stmt2, 'iiid', $oid, $it['book_id'], $it['qty'], $it['price']);
                mysqli_stmt_execute($stmt2);

                $stmt3 = mysqli_prepare($conn, 'UPDATE books SET stock = stock - ? WHERE book_id = ?');
                mysqli_stmt_bind_param($stmt3, 'ii', $it['qty'], $it['book_id']);
                mysqli_stmt_execute($stmt3);
            }

            mysqli_commit($conn);
            unset($_SESSION['cart']);
            $success = 'Order placed successfully! 🎉 Your order number is #' . $oid;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}
?>

<div class="checkout-page">
  <div class="checkout-card">
    <h2>Checkout</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo htmlentities($success); ?></div>
      <a href="/online_bookstore/pages/home.php" class="btn">Back to Home</a>
    <?php else: ?>
      <h4>Order Summary</h4>
      <ul class="order-list">
        <?php foreach ($items as $it): ?>
          <li><?php echo htmlentities($it['title']); ?> × <?php echo intval($it['qty']); ?> — ₱<?php echo number_format($it['price'] * $it['qty'], 2); ?></li>
        <?php endforeach; ?>
      </ul>
      <p class="total"><strong>Total: ₱<?php echo number_format($total, 2); ?></strong></p>

      <form method="POST">
        <button class="btn" name="confirm" type="submit">Confirm & Pay</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
