<?php
include __DIR__ . '/../includes/header.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);
    $qty = max(1, intval($_POST['quantity']));
    if (isset($_SESSION['cart'][$book_id])) $_SESSION['cart'][$book_id] += $qty;
    else $_SESSION['cart'][$book_id] = $qty;
    header('Location:/online_bookstore/pages/cart.php');
    exit;
}

// Remove from cart
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][intval($_GET['remove'])]);
    header('Location:/online_bookstore/pages/cart.php');
    exit;
}
?>

<div class="cart-page">
  <div class="cart-card">
    <h2>Your Cart 🛒</h2>

    <?php if (empty($_SESSION['cart'])): ?>
      <p class="empty-text">Your cart is empty.</p>
      <a href="/online_bookstore/pages/home.php" class="btn">Browse Books</a>
    <?php else: ?>
      <div class="cart-table">
        <table>
          <thead>
            <tr>
              <th>Book</th>
              <th>Qty</th>
              <th>Price</th>
              <th>Subtotal</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $bid => $qty):
              $stmt = mysqli_prepare($conn, 'SELECT title, price FROM books WHERE book_id=?');
              mysqli_stmt_bind_param($stmt, 'i', $bid);
              mysqli_stmt_execute($stmt);
              $res = mysqli_stmt_get_result($stmt);
              $b = mysqli_fetch_assoc($res);
              $sub = $b['price'] * $qty;
              $total += $sub;
            ?>
              <tr>
                <td><?php echo htmlentities($b['title']); ?></td>
                <td><?php echo $qty; ?></td>
                <td>₱<?php echo number_format($b['price'], 2); ?></td>
                <td>₱<?php echo number_format($sub, 2); ?></td>
                <td><a href="?remove=<?php echo $bid; ?>" class="remove">Remove</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="cart-footer">
        <h3>Total: ₱<?php echo number_format($total, 2); ?></h3>
        <a href="/online_bookstore/pages/checkout.php" class="btn">Proceed to Checkout</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
