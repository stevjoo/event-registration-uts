<?php 
session_start(); 

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

if (!isset($_GET['event_id'])) {
    header("Location: manage_events.php"); 
    exit;
}

$event_id = $_GET['event_id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Event not found.";
    exit;
}

$uploadError = '';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'bmp', 'gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    $imageName = $event['image']; 
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
        $image = $_FILES['event_image'];
        $image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($image_ext), $allowed_extensions)) {
            if (!empty($event['image']) && file_exists('../assets/images/' . $event['image'])) {
                unlink('../assets/images/' . $event['image']);
            }

            $imageName = uniqid() . '.' . $image_ext;
            $imagePath = '../assets/images/' . $imageName;
            if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                $uploadError = 'Error uploading image.';
            }
        } else {
            $uploadError = 'Invalid file type for event image. Only jpg, jpeg, png, svg, webp, bmp, gif files are allowed.';
        }
    }

    $bannerName = $event['banner'];
    if (isset($_FILES['event_banner']) && $_FILES['event_banner']['error'] === 0) {
        $banner = $_FILES['event_banner'];
        $banner_ext = pathinfo($banner['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($banner_ext), $allowed_extensions)) {
            if (!empty($event['banner']) && file_exists('../assets/images/' . $event['banner'])) {
                unlink('../assets/images/' . $event['banner']);
            }

            $bannerName = uniqid() . '_banner.' . $banner_ext;
            $bannerPath = '../assets/images/' . $bannerName;
            if (!move_uploaded_file($banner['tmp_name'], $bannerPath)) {
                $uploadError = 'Error uploading banner.';
            }
        } else {
            $uploadError = 'Invalid file type for event banner. Only jpg, jpeg, png, svg, webp, bmp, gif files are allowed.';
        }
    }

    if (empty($uploadError)) {
        $stmt = $pdo->prepare("UPDATE events SET title = :title, description = :description, event_date = :event_date, event_time = :event_time, location = :location, max_participants = :max_participants, status = :status, image = :image, banner = :banner WHERE event_id = :event_id");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'event_date' => $event_date,
            'event_time' => $event_time,
            'location' => $location,
            'max_participants' => $max_participants,
            'status' => $status,
            'image' => $imageName,
            'banner' => $bannerName,
            'event_id' => $event_id
        ]);
        header("Location: manage_events.php");
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
<body class="bg-gradient-to-r from-green-200 to-blue-200">
<br>
    <div class="flex items-center justify-center min-h-screen">
        <div class="form-container shadow-lg transition-transform duration-300 transform hover:scale-105 fade-in">
        
            <h1 class="text-2xl font-bold text-center mb-6">Edit Event</h1>

            <?php if (!empty($uploadError)): ?>
                <p class="text-red-500 text-center mb-4"><?= $uploadError ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="fade-in opacity-0 transition-opacity duration-500 delay-100">
                    <label class="block text-sm font-medium">Event Title:</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-200">
                    <label class="block text-sm font-medium">Description:</label>
                    <textarea name="description" required
                              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($event['description']) ?></textarea>
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-300">
                    <label class="block text-sm font-medium">Event Date:</label>
                    <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-400">
                    <label class="block text-sm font-medium">Event Time:</label>
                    <input type="time" name="event_time" value="<?= htmlspecialchars($event['event_time']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-500">
                    <label class="block text-sm font-medium">Location:</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-600">
                    <label class="block text-sm font-medium">Max Participants:</label>
                    <input type="number" name="max_participants" value="<?= htmlspecialchars($event['max_participants']) ?>" required
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:ring focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-700">
                    <label class="block text-sm font-medium">Status:</label>
                    <select name="status" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="open" <?= $event['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                        <option value="closed" <?= $event['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                        <option value="canceled" <?= $event['status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                    </select>
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-800">
                    <label class="block text-sm font-medium">Event Image (Optional - Current: <?= htmlspecialchars($event['image']) ?>):</label>
                    <input type="file" name="event_image" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif"
                           class="mt-1 w-full text-gray-900 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-900">
                    <label class="block text-sm font-medium">Event Banner (Optional - Current: <?= htmlspecialchars($event['banner']) ?>):</label>
                    <input type="file" name="event_banner" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif"
                           class="mt-1 w-full text-gray-900 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="fade-in opacity-0 transition-opacity duration-500 delay-1000">
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
