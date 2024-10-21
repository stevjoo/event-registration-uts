<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$uploadError = '';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'bmp', 'gif'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];
    $status = 'open';

    $imageName = '';
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $image = $_FILES['event_image'];
        $image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($image_ext), $allowed_extensions)) {
            $imageName = uniqid() . '.' . $image_ext;
            $imagePath = '../uploads/event-images/' . $imageName;
            move_uploaded_file($image['tmp_name'], $imagePath);
        } else {
            $uploadError = 'Invalid file type for event image. Only jpg, jpeg, png, svg, webp, bmp, gif files are allowed.';
        }
    }

    $bannerName = '';
    if (isset($_FILES['event_banner']) && $_FILES['event_banner']['error'] == 0) {
        $banner = $_FILES['event_banner'];
        $banner_ext = pathinfo($banner['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($banner_ext), $allowed_extensions)) {
            $bannerName = uniqid() . '_banner.' . $banner_ext;
            $bannerPath = '../uploads/banner/' . $bannerName;
            move_uploaded_file($banner['tmp_name'], $bannerPath);
        } else {
            $uploadError = 'Invalid file type for event banner. Only jpg, jpeg, png, svg, webp, bmp, gif files are allowed.';
        }
    }

    if (empty($uploadError)) {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, max_participants, status, image, banner) VALUES (:title, :description, :event_date, :event_time, :location, :max_participants, :status, :image, :banner)");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'event_date' => $event_date,
            'event_time' => $event_time,
            'location' => $location,
            'max_participants' => $max_participants,
            'status' => $status,
            'image' => $imageName,
            'banner' => $bannerName
        ]);
        header("Location: manage_events.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: white; 
        }

        .form-container {
            background-color: #2D364C; 
            width: 80%; 
            max-width: 800px;
            padding: 5rem;
            border-radius: 10px;
            color: white;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        }
        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>
    <br>

    <!-- Main Form Section -->
    <div class="flex items-center justify-center min-h-screen">
        <div class="form-container shadow-lg transition-transform duration-300 transform hover:scale-105 fade-in">
        
            <!-- Title of the Form -->
            <h1 class="text-2xl font-bold text-center mb-6">Add New Event</h1>

            <!-- Error Message for File Upload -->
            <?php if (!empty($uploadError)): ?>
                <p class="text-red-500 text-center mb-4"><?= $uploadError ?></p>
            <?php endif; ?>

            <!-- Form for Adding New Event -->
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <!-- Event Title Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-100">
                    <label class="block text-sm font-medium">Event Title:</label>
                    <input type="text" name="title" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Description Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-200">
                    <label class="block text-sm font-medium">Description:</label>
                    <textarea name="description" required
                              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <!-- Event Date Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-300">
                    <label class="block text-sm font-medium">Event Date:</label>
                    <input type="date" name="event_date" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Time Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-400">
                    <label class="block text-sm font-medium">Event Time:</label>
                    <input type="time" name="event_time" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Location Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-500">
                    <label class="block text-sm font-medium">Location:</label>
                    <input type="text" name="location" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Max Participants Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-600">
                    <label class="block text-sm font-medium">Max Participants:</label>
                    <input type="number" name="max_participants" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Image Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-700">
                    <label class="block text-sm font-medium">Event Image (Optional):</label>
                    <input type="file" name="event_image" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif"
                           class="mt-1 w-full text-gray-900 border border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Event Banner Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-800">
                    <label class="block text-sm font-medium">Event Banner (Optional):</label>
                    <input type="file" name="event_banner" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif"
                           class="mt-1 w-full text-gray-900 border border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Submit Button -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-900">
                    <button type="submit"
                            class="w-full py-2 px-4 bg-indigo-600 text-white font-bold rounded-md shadow hover:bg-indigo-700 transition-all duration-300">
                        Add Event
                    </button>
                </div>
            </form>
        </div>
    </div>
    <br>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.fade-in').forEach(el => {
                    el.classList.add('show');
                });
            }, 100);
        });
    </script>
</body>
</html>
