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
            $imagePath = '../assets/images/' . $imageName;
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
            $bannerPath = '../assets/images/' . $bannerName;
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

<?php include '../includes/navbar.php'; ?>

<h1>Add New Event</h1>

<?php if (!empty($uploadError)): ?>
    <p style="color: red;"><?= $uploadError ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Event Title:</label>
    <input type="text" name="title" required>
    
    <label>Description:</label>
    <textarea name="description" required></textarea>
    
    <label>Event Date:</label>
    <input type="date" name="event_date" required>
    
    <label>Event Time:</label>
    <input type="time" name="event_time" required>
    
    <label>Location:</label>
    <input type="text" name="location" required>
    
    <label>Max Participants:</label>
    <input type="number" name="max_participants" required>
    
    <label>Event Image (Optional):</label>
    <input type="file" name="event_image" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif">
    
    <label>Event Banner (Optional):</label>
    <input type="file" name="event_banner" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif">
    
    <button type="submit">Add Event</button>
</form>
