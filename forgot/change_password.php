<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, email, password FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        if ($new_password !== $password_confirm) {
            $errors[] = 'Passwords do not match.';
        }
    }

    if (count($errors) === 0 && !empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $hashed_password, 'id' => $user_id]);

        header("Location: ../login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-200 to-purple-200">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
        <h2 class="text-3xl font-bold text-center text-gray-700 mb-6">Change Password</h2>

        <form method="POST" action="./change_password.php" class="space-y-6">

            <!-- New Password Field -->
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-200">
                <label for="password" class="block text-sm font-medium text-gray-700">New Password:</label>
                <input type="password" name="password" id="password" 
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <!-- Confirm Password Field -->
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-300">
                <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirm New Password:</label>
                <input type="password" name="password_confirm" id="password_confirm" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <?php if (count($errors) > 0): ?>
                <ul class="mt-4 text-center">
                    <?php foreach ($errors as $error): ?>
                        <li class="text-red-500 text-sm"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-400">
                <button type="submit"
                        class="w-full py-2 px-4 bg-indigo-600 text-white font-bold rounded-md shadow hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                    Update Password
                </button>
            </div>

            <div class="text-center opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-500">
                <a href="../login.php" class="text-sm text-indigo-600 hover:underline">Cancel</a>
            </div>

        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('div').forEach(div => {
                    div.classList.remove('opacity-0', 'translate-y-6');
                });
            }, 100);
        });
    </script>
</body>
</html>
