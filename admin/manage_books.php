<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
  header('Location: ../pages/login.php');
  exit;
}

$msg = '';

// Handle adding new author, publisher, category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add_author'])) {
    $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);
    mysqli_query($conn, "INSERT INTO authors (author_name) VALUES ('{$author_name}')");
    $msg = "✅ Author added successfully!";
  } elseif (isset($_POST['add_publisher'])) {
    $publisher_name = mysqli_real_escape_string($conn, $_POST['publisher_name']);
    mysqli_query($conn, "INSERT INTO publishers (publisher_name) VALUES ('{$publisher_name}')");
    $msg = "✅ Publisher added successfully!";
  } elseif (isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    mysqli_query($conn, "INSERT INTO categories (category_name) VALUES ('{$category_name}')");
    $msg = "✅ Category added successfully!";
  } elseif (isset($_POST['add_book'])) {
    // Add book
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author_id = intval($_POST['author_id']);
    $publisher_id = intval($_POST['publisher_id']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    mysqli_query($conn, "
      INSERT INTO books (title, author_id, publisher_id, category_id, price, stock, description, cover_image)
      VALUES ('{$title}', {$author_id}, {$publisher_id}, {$category_id}, {$price}, {$stock}, '{$desc}', 'default.png')
    ");
    $msg = '✅ Book added successfully!';
  }
}

// Handle delete
if (isset($_GET['delete'])) {
  $bid = intval($_GET['delete']);
  mysqli_query($conn, "DELETE FROM books WHERE book_id={$bid}");
  $msg = '🗑️ Book deleted successfully.';
}

// Fetch dropdown data
$authors = mysqli_query($conn, "SELECT * FROM authors ORDER BY author_name ASC");
$publishers = mysqli_query($conn, "SELECT * FROM publishers ORDER BY publisher_name ASC");
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");

// Fetch books with joined info
$res = mysqli_query($conn, "
  SELECT b.*, a.author_name, p.publisher_name, c.category_name
  FROM books b
  LEFT JOIN authors a ON b.author_id = a.author_id
  LEFT JOIN publishers p ON b.publisher_id = p.publisher_id
  LEFT JOIN categories c ON b.category_id = c.category_id
  ORDER BY b.book_id DESC
");
?>

<div class="manage-books-page">
  <section class="manage-header">
    <h1>📚 Manage Books</h1>
    <p>Add new books, authors, publishers, and categories — all in one place.</p>
  </section>

  <?php if ($msg): ?>
    <div class="alert-box"><?php echo htmlentities($msg); ?></div>
  <?php endif; ?>

  <div class="manage-container">

    <!-- Add Book Form -->
    <div class="book-card add-book">
      <h3>➕ Add New Book</h3>
      <form method="POST">
        <input class="form-input" name="title" placeholder="Book Title" required>

        <select class="form-input" name="author_id" required>
          <option value="">Select Author</option>
          <?php mysqli_data_seek($authors, 0); while ($a = mysqli_fetch_assoc($authors)): ?>
            <option value="<?php echo $a['author_id']; ?>"><?php echo htmlentities($a['author_name']); ?></option>
          <?php endwhile; ?>
        </select>

        <select class="form-input" name="publisher_id" required>
          <option value="">Select Publisher</option>
          <?php mysqli_data_seek($publishers, 0); while ($p = mysqli_fetch_assoc($publishers)): ?>
            <option value="<?php echo $p['publisher_id']; ?>"><?php echo htmlentities($p['publisher_name']); ?></option>
          <?php endwhile; ?>
        </select>

        <select class="form-input" name="category_id" required>
          <option value="">Select Category</option>
          <?php mysqli_data_seek($categories, 0); while ($c = mysqli_fetch_assoc($categories)): ?>
            <option value="<?php echo $c['category_id']; ?>"><?php echo htmlentities($c['category_name']); ?></option>
          <?php endwhile; ?>
        </select>

        <input class="form-input" name="price" placeholder="Price (₱)" type="number" step="0.01" required>
        <input class="form-input" name="stock" placeholder="Stock Quantity" type="number" required>
        <textarea class="form-input" name="description" placeholder="Book Description" rows="3"></textarea>
        <button class="btn-submit" name="add_book" type="submit">Add Book</button>
      </form>
    </div>

    <!-- Add Author/Publisher/Category -->
    <div class="book-card add-meta">
      <h3>🧩 Add Author / Publisher / Category</h3>

      <form method="POST">
        <input class="form-input" name="author_name" placeholder="New Author Name">
        <button class="btn-submit" name="add_author" type="submit">Add Author</button>
      </form>

      <form method="POST">
        <input class="form-input" name="publisher_name" placeholder="New Publisher Name">
        <button class="btn-submit" name="add_publisher" type="submit">Add Publisher</button>
      </form>

      <form method="POST">
        <input class="form-input" name="category_name" placeholder="New Category Name">
        <button class="btn-submit" name="add_category" type="submit">Add Category</button>
      </form>
    </div>

    <!-- All Books List -->
    <div class="book-card book-list scrollable-card">
  <div class="table-header sticky-header">
    <h3>📖 All Books</h3>
    <p class="subtext">View and manage every book in your inventory</p>
  </div>

  <div class="table-container">
    <table class="book-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Author</th>
          <th>Publisher</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($res) > 0): ?>
          <?php while ($b = mysqli_fetch_assoc($res)): ?>
            <tr>
              <td><?php echo intval($b['book_id']); ?></td>
              <td class="book-title"><?php echo htmlentities($b['title']); ?></td>
              <td><?php echo htmlentities($b['author_name']); ?></td>
              <td><?php echo htmlentities($b['publisher_name']); ?></td>
              <td><?php echo htmlentities($b['category_name']); ?></td>
              <td><span class="price">₱<?php echo number_format($b['price'], 2); ?></span></td>
              <td><?php echo intval($b['stock']); ?></td>
              <td>
                <a class="btn-action btn-view" href="?view=<?php echo $b['book_id']; ?>">View</a>
                <a class="btn-action btn-delete" href="?delete=<?php echo $b['book_id']; ?>" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="no-data">No books available.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

    </div>

  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
