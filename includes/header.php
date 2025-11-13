<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BookHaven 📚</title>
  <link rel="icon" type="image/png" href="../assets/images/logo.png">
  <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
<header class="bh-header">
  <div class="bh-container nav-flex">
    <div class="bh-branding">
      <img src="/online_bookstore/assets/images/logo.png" class="bh-logo" alt="BookHaven logo">
      <h1 class="bh-title">Book<span>Haven</span></h1>
    </div>
    <nav class="bh-menu">
      <a href="/online_bookstore/pages/home.php">Home</a>
      <a href="/online_bookstore/pages/books.php">Shop</a>
      <a href="/online_bookstore/pages/cart.php">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
      <?php if (isset($_SESSION['user'])): ?>
        <a href="/online_bookstore/pages/my_orders.php">My Orders</a>
        <?php if (strtolower($_SESSION['user']['role']) === 'admin'): ?>
          <a href="/online_bookstore/admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <a href="/online_bookstore/logout.php">Logout</a>
      <?php else: ?>
        <a href="/online_bookstore/pages/login.php">Login</a>
        <a href="/online_bookstore/pages/register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="bh-main">
