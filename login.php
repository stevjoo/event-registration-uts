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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-blue-200 to-purple-200">
    <img src="./asset/Event-ly.png" class="animate-bounce w-32 mb-5 object-contain">
    <div class="w-full max-w-md bg-white/75 p-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
        <h2 class="text-3xl font-bold text-center text-gray-700 mb-6">Login</h2>

        <form method="POST" action="" class="space-y-6">
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-100">
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="email" id="email" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-300">
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" name="password" id="password" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-500">
                <button type="submit"
                        class="w-full py-2 px-4 bg-indigo-600 text-white font-bold rounded-md shadow hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                    Login
                </button>
            </div>

            <div class="text-center opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-700">
                <a href="./forgot/confirmation.php" class="text-sm text-indigo-600 hover:underline">Forgot Password?</a>
            </div>
        </form>

        <?php if (isset($error)): ?>
            <p class="mt-4 text-center text-red-600"><?= $error ?></p>
        <?php endif; ?>
    </div>

    <div class="mt-10">
        <a href="index.php" 
           class="py-2 px-6 bg-white/50 text-gray-500 border border-white rounded-full shadow-md hover:bg-white hover:text-gray-700 transition-transform transform hover:scale-105 focus:ring-4 focus:ring-white focus:ring-opacity-50">
            ← Back to Home
        </a>
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