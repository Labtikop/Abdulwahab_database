<?php include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../config/db.php'; $msg=''; if($_SERVER['REQUEST_METHOD']==='POST'){ $name=mysqli_real_escape_string($conn,$_POST['full_name']); $email=mysqli_real_escape_string($conn,$_POST['email']); $pass=$_POST['password']; $hash=password_hash($pass,PASSWORD_DEFAULT); $stmt=mysqli_prepare($conn,'INSERT INTO users (full_name,email,password) VALUES (?,?,?)'); mysqli_stmt_bind_param($stmt,'sss',$name,$email,$hash); mysqli_stmt_execute($stmt); $msg='Registered. You can login.'; } ?>
<div class="auth-page">
  <div class="auth-card register">
    <h2>Create Your <span style="color:var(--accent-2)">BookHaven</span> Account</h2>

    <?php if ($msg): ?>
      <div class="alert" style="background:#264e35;color:#a7ffb0;border-radius:8px;padding:8px;margin-bottom:12px;">
        <?php echo htmlentities($msg); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <input name="full_name" placeholder="Full Name" required>
      <input name="email" type="email" placeholder="Email" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>

    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>