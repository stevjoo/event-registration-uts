<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match';
        }
    }

    if (count($errors) === 0) {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->execute(['name' => $name, 'email' => $email, 'id' => $user_id]);

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashed_password, 'id' => $user_id]);
        }

        header("Location: view_profile.php");
        exit;
    }
}
?>

<?php include '../includes/navbar.php'; ?>

<div class="container mx-auto my-10">
    <h1 class="text-center text-2xl font-bold my-6">Edit Profile</h1>

    <form action="edit_profile.php" method="POST" class="bg-white p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-bold mb-2">New Password (optional):</label>
            <input type="password" id="password" name="password" class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label for="password_confirm" class="block text-gray-700 font-bold mb-2">Confirm New Password:</label>
            <input type="password" id="password_confirm" name="password_confirm" class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <?php if (count($errors) > 0): ?>
            <ul class="mb-4">
                <?php foreach ($errors as $error): ?>
                    <li class="text-red-500"><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition w-full text-sm sm:w-auto">Update Profile</button>
            <a href="view_profile.php" class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 transition w-full sm:w-auto text-center">Cancel</a>
        </div>

    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">