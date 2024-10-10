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

<h1>Edit Event</h1>

<?php if (!empty($uploadError)): ?>
    <p style="color: red;"><?= $uploadError ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Event Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
    
    <label>Description:</label>
    <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea>
    
    <label>Event Date:</label>
    <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required>
    
    <label>Event Time:</label>
    <input type="time" name="event_time" value="<?= htmlspecialchars($event['event_time']) ?>" required>
    
    <label>Location:</label>
    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
    
    <label>Max Participants:</label>
    <input type="number" name="max_participants" value="<?= htmlspecialchars($event['max_participants']) ?>" required>
    
    <label>Status:</label>
    <select name="status">
        <option value="open" <?= $event['status'] == 'open' ? 'selected' : '' ?>>Open</option>
        <option value="closed" <?= $event['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
        <option value="canceled" <?= $event['status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
    </select>
    
    <label>Event Image (Optional - Current: <?= htmlspecialchars($event['image']) ?>):</label>
    <input type="file" name="event_image" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif">
    
    <label>Event Banner (Optional - Current: <?= htmlspecialchars($event['banner']) ?>):</label>
    <input type="file" name="event_banner" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif">
    
    <button type="submit">Update Event</button>
</form>
