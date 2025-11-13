<?php 
include __DIR__ . '/../includes/header.php'; 
include __DIR__ . '/../config/db.php'; 
?>

<div class="shop-page">
  <div class="shop-container">
    <h2 class="shop-title">Our Book Collection</h2>

    <div class="book-grid">
      <?php 
      $res = mysqli_query($conn, "SELECT b.*, a.author_name FROM books b LEFT JOIN authors a ON b.author_id=a.author_id ORDER BY b.title ASC"); 
      if (!$res || mysqli_num_rows($res) === 0): 
      ?>
        <div class="no-books">
          <p>No books available at the moment.</p>
        </div>
      <?php 
      else:
        while($b = mysqli_fetch_assoc($res)): 
      ?>
        <div class="book-card">
          <div class="book-img">
            <img src="/online_bookstore/assets/images/<?php echo $b['cover_image'] ?: 'default.png'; ?>" alt="cover">
          </div>
          <div class="book-content">
            <h3 class="book-title"><?php echo htmlentities($b['title']); ?></h3>
            <p class="book-author">by <?php echo htmlentities($b['author_name']); ?></p>
            <p class="book-price">₱<?php echo number_format($b['price'], 2); ?></p>
            <a href="/online_bookstore/pages/book_details.php?id=<?php echo $b['book_id']; ?>" class="btn-view">View Details</a>
          </div>
        </div>
      <?php endwhile; endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
