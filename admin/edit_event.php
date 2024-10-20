<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['event_id'])) {
    header("Location: manage_events.php"); 
    exit;
}

$event_id = $_GET['event_id'];
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch();

if (!$event) {
    header("Location: manage_events.php"); 
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
    $status = $_POST['status'];

    $imageName = $event['image']; 
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $image = $_FILES['event_image'];
        $image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($image_ext), $allowed_extensions)) {
            if (!empty($event['image']) && file_exists('../assets/images/' . $event['image'])) {
                unlink('../assets/images/' . $event['image']);
            }

            $imageName = uniqid() . '.' . $image_ext;
            $imagePath = '../assets/images/' . $imageName;
            move_uploaded_file($image['tmp_name'], $imagePath);
        } else {
            $uploadError = 'Invalid file type for event image. Only jpg, jpeg, png, svg, webp, bmp, gif files are allowed.';
        }
    }

    $bannerName = $event['banner'];
    if (isset($_FILES['event_banner']) && $_FILES['event_banner']['error'] == 0) {
        $banner = $_FILES['event_banner'];
        $banner_ext = pathinfo($banner['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($banner_ext), $allowed_extensions)) {
            if (!empty($event['banner']) && file_exists('../assets/images/' . $event['banner'])) {
                unlink('../assets/images/' . $event['banner']);
            }

            $bannerName = uniqid() . '_banner.' . $banner_ext;
            $bannerPath = '../assets/images/' . $bannerName;
            move_uploaded_file($banner['tmp_name'], $bannerPath);
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
<script src="https://cdn.tailwindcss.com"></script>

<h1 class="text-center text-2xl font-bold my-6 lg:my-8">Edit Event</h1>

<?php if (!empty($uploadError)): ?>
    <p class="text-center text-red-500 font-semibold mb-4"><?= $uploadError ?></p>
<?php endif; ?>

<div class="max-w-3xl mx-auto bg-white p-6 md:p-10 shadow-lg rounded-lg mb-12">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label class="block text-lg font-medium text-gray-700 mb-1">Event Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-lg font-medium text-gray-700 mb-1">Description:</label>
            <textarea name="description" required class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="5"><?= htmlspecialchars($event['description']) ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-1">Event Date:</label>
                <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-lg font-medium text-gray-700 mb-1">Event Time:</label>
                <input type="time" name="event_time" value="<?= htmlspecialchars($event['event_time']) ?>" required class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-lg font-medium text-gray-700 mb-1">Location:</label>
            <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-lg font-medium text-gray-700 mb-1">Max Participants:</label>
            <input type="number" name="max_participants" value="<?= htmlspecialchars($event['max_participants']) ?>" required class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-lg font-medium text-gray-700 mb-1">Status:</label>
            <select name="status" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="open" <?= $event['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                <option value="closed" <?= $event['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                <option value="canceled" <?= $event['status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
            </select>
        </div>

        <div>
            <label class="block text-lg font-medium text-gray-700 mb-1">Event Image (Optional - Current: <?= htmlspecialchars($event['image']) ?>):</label>
            <input type="file" name="event_image" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none">
        </div>

        <div>
            <label class="block text-lg font-medium text-gray-700 mb-1">Event Banner (Optional - Current: <?= htmlspecialchars($event['banner']) ?>):</label>
            <input type="file" name="event_banner" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none">
        </div>

        <div class="text-center flex justify-center space-x-4">
            <button type="submit" class="bg-blue-500 text-white font-semibold px-6 py-3 rounded-md shadow-md hover:bg-blue-600 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300">
                Update Event
            </button>
            <a href="manage_events.php" class="bg-red-500 text-white font-semibold px-6 py-3 rounded-md shadow-md hover:bg-red-700 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-gray-300">
                Cancel
            </a>
        </div>
    </form> 
</div>
