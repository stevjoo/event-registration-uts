<?php
session_start();
require '../includes/db_connection.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));

    if (empty($name)) {
        $error = "Please enter a username.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['role'] == 'admin') {
                $error = "Admin users are not allowed to use the forgot password feature.";
            } else {
                if (!empty($email) && !empty($phone)) {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND phone = :phone");
                    $stmt->execute(['email' => $email, 'phone' => $phone]);
                    $user_details = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user_details) {
                        $_SESSION['user_id'] = $user_details['id'];
                        header("Location: ./change_password.php");
                        exit();
                    } else {
                        $error = "Invalid email or phone number.";
                    }
                } else {
                    $error = "Please enter both email and phone number.";
                }
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-200 to-purple-200">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
        <h2 class="text-3xl font-bold text-center text-gray-700 mb-6">Forgot Password</h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg">
                <p class="text-center"><?= $error ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">

            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-100">
                <label for="name" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-200">
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="email" id="email" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-300">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone:</label>
                <input type="text" name="phone" id="phone" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm">
            </div>

            <div class="opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-400">
                <button type="submit"
                        class="w-full py-2 px-4 bg-indigo-600 text-white font-bold rounded-md shadow hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                    Next
                </button>
            </div>
        </form>

        <div class="text-center mt-8 opacity-0 translate-y-6 transition-opacity transition-transform duration-500 delay-500">
            <a href="../login.php" class="text-sm text-indigo-600 hover:underline">Cancel</a>
        </div>
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
