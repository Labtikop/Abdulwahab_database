<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../config/db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
  echo '<div class="book-page"><div class="book-card"><p>Book not found.</p></div></div>';
  include __DIR__ . '/../includes/footer.php';
  exit;
}

$stmt = mysqli_prepare($conn, 'SELECT b.*, a.author_name FROM books b LEFT JOIN authors a ON b.author_id=a.author_id WHERE b.book_id=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$b = mysqli_fetch_assoc($res);

if (!$b) {
  echo '<div class="book-page"><div class="book-card"><p>Book not found.</p></div></div>';
  include __DIR__ . '/../includes/footer.php';
  exit;
}
?>

<div class="book-page">
  <div class="book-card">
    <div class="book-flex">
      <div class="book-image">
        <img src="/online_bookstore/assets/images/<?php echo $b['cover_image'] ?: 'default.png'; ?>" alt="Book cover">
      </div>

      <div class="book-info">
        <h2><?php echo htmlentities($b['title']); ?></h2>
        <p class="book-author">by <?php echo htmlentities($b['author_name']); ?></p>
        <p class="book-description"><?php echo nl2br(htmlentities($b['description'])); ?></p>
        <p class="book-price">₱<?php echo number_format($b['price'], 2); ?></p>

        <form method="POST" action="/online_bookstore/pages/cart.php" class="add-to-cart-form">
          <input type="hidden" name="book_id" value="<?php echo $b['book_id']; ?>">
          <label for="quantity">Quantity:</label>
          <input type="number" id="quantity" name="quantity" value="1" min="1" class="qty-input">
          <button class="btn" name="add_to_cart" type="submit">Add to Cart</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
