<?php
session_start();
require 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin/manage_events.php");
        } else {
            header("Location: user/view_events.php");
        }
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<form method="POST" action="">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Login</button>
    <a href="./forgot/forgot_password.php">Forgot Password</a>
</form>

<?php if (isset($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

