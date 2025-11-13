<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
  header('Location: ../pages/login.php');
  exit;
}

$msg = '';
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
  $name = mysqli_real_escape_string($conn, $_POST['full_name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];
  $role = $_POST['role'] === 'Admin' ? 'Admin' : 'Customer';
  mysqli_query($conn, "INSERT INTO users (full_name, email, password, role) VALUES ('{$name}', '{$email}', '{$password}', '{$role}')");
  $msg = '✅ User added successfully!';
}

if ($action === 'delete' && isset($_GET['id'])) {
  $uid = intval($_GET['id']);
  $r = mysqli_query($conn, "SELECT role FROM users WHERE user_id={$uid} LIMIT 1");
  if ($r && mysqli_num_rows($r) > 0) {
    $u = mysqli_fetch_assoc($r);
    if (strtolower($u['role']) === 'admin') {
      $msg = '⚠️ Cannot delete an Admin account.';
    } else {
      mysqli_query($conn, "DELETE FROM users WHERE user_id={$uid}");
      $msg = '🗑️ User deleted successfully.';
    }
  }
}

$edit_user = null;
if ($action === 'edit' && isset($_GET['id'])) {
  $uid = intval($_GET['id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'] === 'Admin' ? 'Admin' : 'Customer';
    $pw = trim($_POST['password'] ?? '');
    if ($pw !== '')
      mysqli_query($conn, "UPDATE users SET full_name='{$name}', email='{$email}', password='{$pw}', role='{$role}' WHERE user_id={$uid}");
    else
      mysqli_query($conn, "UPDATE users SET full_name='{$name}', email='{$email}', role='{$role}' WHERE user_id={$uid}");
    $msg = '✅ User updated successfully.';
  }
  $res = mysqli_query($conn, "SELECT * FROM users WHERE user_id={$uid} LIMIT 1");
  $edit_user = $res ? mysqli_fetch_assoc($res) : null;
}

$res = mysqli_query($conn, 'SELECT * FROM users ORDER BY user_id DESC');
?>

<div class="manage-users-page">
  <section class="manage-header">
    <h1>👥 Manage Users</h1>
    <p>Add, edit, or remove user accounts and assign their roles.</p>
  </section>

  <?php if ($msg): ?>
    <div class="alert-box"><?php echo htmlentities($msg); ?></div>
  <?php endif; ?>

  <div class="manage-container">
    <!-- Add User Form -->
    <div class="user-card add-user">
      <h3>➕ Add User</h3>
      <form method="POST">
        <input class="form-input" name="full_name" placeholder="Full Name" required>
        <input class="form-input" name="email" type="email" placeholder="Email Address" required>
        <input class="form-input" name="password" placeholder="Password (plain text)">
        <select name="role" class="form-input">
          <option>Customer</option>
          <option>Admin</option>
        </select>
        <button class="btn-submit" name="add_user" type="submit">Add User</button>
      </form>
    </div>

    <!-- Edit User Form -->
    <?php if ($edit_user): ?>
    <div class="user-card edit-user">
      <h3>✏️ Edit User</h3>
      <form method="POST">
        <input class="form-input" name="full_name" value="<?php echo htmlentities($edit_user['full_name']); ?>" required>
        <input class="form-input" name="email" value="<?php echo htmlentities($edit_user['email']); ?>" type="email" required>
        <input class="form-input" name="password" placeholder="New password (leave blank to keep)">
        <select name="role" class="form-input">
          <option <?php echo $edit_user['role']==='Customer'?'selected':''; ?>>Customer</option>
          <option <?php echo $edit_user['role']==='Admin'?'selected':''; ?>>Admin</option>
        </select>
        <button class="btn-submit" name="save_user" type="submit">Save Changes</button>
      </form>
    </div>
    <?php endif; ?>

    <!-- All Users Table -->
    <div class="user-card user-list">
      <h3>📋 All Users</h3>
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php while ($u = mysqli_fetch_assoc($res)): ?>
              <tr>
                <td><?php echo intval($u['user_id']); ?></td>
                <td><?php echo htmlentities($u['full_name']); ?></td>
                <td><?php echo htmlentities($u['email']); ?></td>
                <td><?php echo htmlentities($u['role']); ?></td>
                <td>
                  <a class="btn-edit" href="?action=edit&id=<?php echo $u['user_id']; ?>">Edit</a>
                  <?php if (strtolower($u['role']) !== 'admin'): ?>
                    <a class="btn-delete" href="?action=delete&id=<?php echo $u['user_id']; ?>" onclick="return confirm('Delete this user?')">Delete</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
