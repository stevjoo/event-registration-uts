<?php 
session_start(); // Mulai session di awal file

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}

include '../includes/navbar.php'; 

try {
    $pdo = new PDO('mysql:host=localhost;dbname=event_management', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "Event not found.";
        exit;
    }
} else {
    echo "No event ID provided.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];
    $uploadError = '';

    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $imageFile = $_FILES['event_image'];
        if (in_array($imageFile['type'], $allowedTypes)) {
            $imageName = time() . '-' . basename($imageFile['name']);
            $imagePath = '../uploads/images/' . $imageName;
            if (!move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
                $uploadError = 'Error uploading image.';
            }
        } else {
            $uploadError = 'Invalid image type.';
        }
    } else {
        $imageName = $event['event_image'];
    }

    if (isset($_FILES['event_banner']) && $_FILES['event_banner']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $bannerFile = $_FILES['event_banner'];
        if (in_array($bannerFile['type'], $allowedTypes)) {
            $bannerName = time() . '-' . basename($bannerFile['name']);
            $bannerPath = '../uploads/banners/' . $bannerName;
            if (!move_uploaded_file($bannerFile['tmp_name'], $bannerPath)) {
                $uploadError = 'Error uploading banner.';
            }
        } else {
            $uploadError = 'Invalid banner type.';
        }
    } else {
        $bannerName = $event['event_banner']; 
    }

    if (empty($uploadError)) {
        $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, event_time = ?, location = ?, max_participants = ?, event_image = ?, event_banner = ?
                               WHERE event_id = ?");
        $stmt->execute([$title, $description, $event_date, $event_time, $location, $max_participants, $imageName, $bannerName, $event_id]);

        header('Location: /admin/view_events.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
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
<br>
    <div class="flex items-center justify-center min-h-screen">
        <div class="form-container shadow-lg transition-transform duration-300 transform hover:scale-105 fade-in">
        
            <h1 class="text-2xl font-bold text-center mb-6">Edit Event</h1>

            <?php if (!empty($uploadError)): ?>
                <p class="text-red-500 text-center mb-4"><?= $uploadError ?></p>
            <?php endif; ?>

            <!-- Form for Editing Event -->
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <!-- Event Title Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-100">
                    <label class="block text-sm font-medium">Event Title:</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Description Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-200">
                    <label class="block text-sm font-medium">Description:</label>
                    <textarea name="description" required
                              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($event['description']) ?></textarea>
                </div>

                <!-- Event Date Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-300">
                    <label class="block text-sm font-medium">Event Date:</label>
                    <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Time Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-400">
                    <label class="block text-sm font-medium">Event Time:</label>
                    <input type="time" name="event_time" value="<?= htmlspecialchars($event['event_time']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Location Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-500">
                    <label class="block text-sm font-medium">Location:</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Max Participants Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-600">
                    <label class="block text-sm font-medium">Max Participants:</label>
                    <input type="number" name="max_participants" value="<?= htmlspecialchars($event['max_participants']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Event Image Input -->
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-700">
                    <label class="block text-sm font-medium">Event Image (Optional):</label>
                    <input type="file" name="event_image" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif"
                           class="mt-1 w-full text-gray-900 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-800">
                    <label class="block text-sm font-medium">Event Banner (Optional):</label>
                    <input type="file" name="event_banner" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif"
                           class="mt-1 w-full text-gray-900 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-900">
                    <button type="submit"
                            class="w-full py-2 px-4 bg-indigo-600 text-white font-bold rounded-md shadow hover:bg-indigo-700 transition-all duration-300">
                        Update Event
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
