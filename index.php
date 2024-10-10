<!-- <?php include 'includes/navbar.php'; ?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Content Section -->
    <div class="min-h-screen flex flex-col justify-center items-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to the Event Management System</h1>
            <p class="text-gray-600 mb-8">Manage events and registrations efficiently with our system.</p>

            <div class="space-x-4">
                <a href="login.php" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500 transition">Login</a>
                <a href="register.php" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-500 transition">Register</a>
            </div>
        </div>
    </div>

    <!-- Conditional View for User -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
        <div class="container mx-auto mt-8 text-center">
            <a href="user/view_events.php" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-400 transition">View Events</a>
        </div>
    <?php endif; ?>

</body>
</html>
