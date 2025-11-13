<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../config/db.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email==''||$password=='') $error='Please enter email and password.';
    else {
        $stmt=mysqli_prepare($conn,'SELECT * FROM users WHERE email=? LIMIT 1');
        mysqli_stmt_bind_param($stmt,'s',$email);
        mysqli_stmt_execute($stmt);
        $res=mysqli_stmt_get_result($stmt);
        if($res && mysqli_num_rows($res)===1) {
            $user=mysqli_fetch_assoc($res);
            $stored=$user['password'];
            $ok=false;
            if((strpos($stored,'$2y$')===0 || strpos($stored,'$2a$')===0) && password_verify($password,$stored)) $ok=true;
            elseif($password===$stored) $ok=true;
            if($ok) {
                $_SESSION['user']=['user_id'=>$user['user_id'],'full_name'=>$user['full_name'],'email'=>$user['email'],'role'=>$user['role']];
                if(strtolower($user['role'])==='admin'){ header('Location: ../admin/dashboard.php'); exit; } else { header('Location: home.php'); exit; }
            } else $error='Invalid email or password.';
        } else $error='Invalid email or password.';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="auth-page">
  <div class="auth-card">
    <h2>Welcome to <span style="color:var(--accent-2)">BookHaven</span></h2>
    <p class="small-muted mb-3">Sign in to manage or continue shopping</p>

    <?php if ($error): ?>
      <div class="alert" style="background:#3b1d4f;color:#ffbaba;border-radius:8px;padding:8px;margin-bottom:12px;">
        <?php echo htmlentities($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign In</button>
    </form>

    
    <p class="mt-2">Don’t have an account? <a href="register.php">Register here</a></p>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>