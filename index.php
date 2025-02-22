<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
</head>
<body class="bg-gradient-to-r from-green-200 to-blue-200" onload="AOS.init()">
    <div class="min-h-screen flex flex-col justify-center items-center">
        <img src="./asset/Event-ly.png" class="animate-bounce w-32 mb-5 object-contain">
        <div class="text-center mb-8 bg-white/50 p-4 rounded-lg">
            <h1 class="text-4xl font-bold text-gray-700 mb-4">Welcome to Event-ly</h1>
            <p class="text-gray-500">Manage events and registrations efficiently with our system.</p>
        </div>

        <div class="flex flex-col md:flex-row justify-center gap-4 mt-12">
            <div class="bg-white/50 shadow-lg rounded-lg p-6 transition-transform transform hover:scale-105 hover:bg-gray-200/50 hover:shadow-xl w-full md:w-1/4" data-aos="fade-up" data-aos-delay="100">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Get Started with Login</h2>
                <p class="text-gray-600 mb-4">Login to access your personalized event dashboard, view and manage your registrations.</p>
                <a href="login.php" class="block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500 transition-all text-center">Login</a>
            </div>

            <div class="bg-white/50 shadow-lg rounded-lg p-6 transition-transform transform hover:scale-105 hover:bg-gray-200/50 hover:shadow-xl w-full md:w-1/4" data-aos="fade-up" data-aos-delay="300">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">New User?</h2>
                <p class="text-gray-600 mb-4">Create a new account to start using the Event Management System and manage your events.</p>
                <a href="register.php" class="block bg-green-600 text-white px-6 py-2 rounded hover:bg-green-500 transition-all text-center">Register</a>
            </div>
        </div>
    </div>

    <br>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
        <div class="container mx-auto text-center mt-8">
            <div class="bg-white shadow-lg rounded-lg p-6 transition-transform transform hover:scale-105 hover:bg-gray-200 hover:shadow-xl mx-auto w-full md:w-1/4" data-aos="fade-up" data-aos-delay="500">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Your Dashboard</h2>
                <p class="text-gray-600 mb-4">As a registered user, you can view your upcoming events and manage your registrations.</p>
                <a href="user/view_events.php" class="block bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-400 transition-all text-center">View Events</a>
            </div>
        </div>
    <?php endif; ?>

    <script>
        AOS.init({
            duration: 1200,  
            once: true,     
        });
    </script>
</body>
</html>
