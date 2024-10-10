<?php
session_start();
require '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim(htmlspecialchars($_POST['name'])); 

    if (empty($name)) {
        $error = "Please enter a username.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            header("Location: ./confirmation.php");
            exit();
        } else {
            $error = "User not found.";
        }
    }
}
?>

<form method="POST" action="">
    <label for="name">Username:</label>
    <input type="text" name="name" id="name" required>
    <button type="submit">Next</button>
</form>

<?php if (isset($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>
