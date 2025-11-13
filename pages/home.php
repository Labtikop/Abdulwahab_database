<?php 
include __DIR__ . '/../includes/header.php'; 
include __DIR__ . '/../config/db.php'; 
?>

<div class="home-page">
  <section class="hero-section">
    <div class="hero-content">
      <h1>Welcome to <span>BookHaven</span></h1>
      <p>Your cozy digital bookstore — explore stories, ideas, and imagination all in one place.</p>
      <a href="/online_bookstore/pages/books.php" class="btn-explore">Browse All Books</a>
    </div>
  </section>

  <section class="featured-section">
    <h2 class="section-title">📚 Featured Books</h2>

    <div class="book-grid">
      <?php 
      $res = mysqli_query($conn, 'SELECT * FROM books LIMIT 9'); 
      if (!$res || mysqli_num_rows($res) === 0): 
      ?>
        <div class="no-books"><p>No featured books at the moment.</p></div>
      <?php else: while($b = mysqli_fetch_assoc($res)): ?>
        <div class="book-card">
          <div class="book-img">
            <img src="/online_bookstore/assets/images/<?php echo $b['cover_image'] ?: 'default.png'; ?>" alt="cover">
          </div>
          <div class="book-content">
            <h3 class="book-title"><?php echo htmlentities($b['title']); ?></h3>
            <p class="book-price">₱<?php echo number_format($b['price'], 2); ?></p>
            <a href="/online_bookstore/pages/book_details.php?id=<?php echo $b['book_id']; ?>" class="btn-view">View Details</a>
          </div>
        </div>
      <?php endwhile; endif; ?>
    </div>
  </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
