<?php
session_start();
require '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $phone) {
        $_SESSION['user_id'] = $user['id'];
            header("Location: ./change_password.php");
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<form method="POST" action="">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone" required>
    <button type="submit">Next</button>
</form>

<?php if (isset($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>
