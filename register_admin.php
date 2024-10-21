<?php
session_start();
require 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, 'admin')");
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => $password
    ]);

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-green-200 to-blue-200">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
        <h2 class="text-3xl font-bold text-center text-gray-700 mb-6">Register</h2>

        <form method="POST" action="" class="space-y-6">
            <!-- Name Field -->
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-100">
                <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm"
                       value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
            </div>

            <!-- Email Field -->
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-300">
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="email" id="email" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm"
                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>

            <!-- Phone Field -->
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-500">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number:</label>
                <input type="tel" name="phone" id="phone" placeholder="08XXXXXXXXXX" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm"
                       value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>">
            </div>

            <!-- Password Field -->
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-700">
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" name="password" id="password" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <!-- Display error if exists -->
            <?php if (isset($error)): ?>
                <div class="text-red-500 text-center">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-900">
                <button type="submit"
                        class="w-full py-2 px-4 bg-green-600 text-white font-bold rounded-md shadow hover:bg-green-700 focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                    Register
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6">
        <a href="index.php" 
           class="py-2 px-6 bg-transparent text-white border border-white rounded-full shadow-md hover:bg-white hover:text-gray-700 transition-transform transform hover:scale-105 focus:ring-4 focus:ring-white focus:ring-opacity-50">
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
