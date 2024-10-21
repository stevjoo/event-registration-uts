<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details including phone number
$stmt = $pdo->prepare("SELECT name, email, phone, avatar FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

// Fetch registered events
$stmt = $pdo->prepare("
    SELECT e.title, e.event_date, e.event_time, e.location 
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id 
    WHERE r.id = :user_id
    ORDER BY e.event_date DESC
");
$stmt->execute(['user_id' => $user_id]);
$events = $stmt->fetchAll();
?>

<?php include '../includes/navbar.php'; ?>

<div class="container mx-auto my-10">
    <h1 class="text-center text-2xl font-bold my-6">Your Profile</h1>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Personal Information</h2>

        <!-- Display profile picture -->
        <div class="flex items-center space-x-4 mb-4">
            <?php
            // Determine avatar file path
            if (!empty($user['avatar'])):
                $avatar_path = '../uploads/avatars/' . htmlspecialchars($user['avatar']);
                if (file_exists($avatar_path)): 
            ?>
                    <img src="<?= $avatar_path ?>" alt="Profile Avatar" class="w-24 h-24 rounded-full border border-gray-300 object-cover">
            <?php else: ?>
                    <!-- If avatar file doesn't exist, show default avatar -->
                    <img src="../uploads/avatars/default-avatar.png" alt="Profile Avatar" class="w-24 h-24 rounded-full border border-gray-300 object-cover">
            <?php endif; ?>
            <?php else: ?>
                <!-- If no avatar is set, show default avatar -->
                <img src="../uploads/avatars/default-avatar.png" alt="Profile Avatar" class="w-24 h-24 rounded-full border border-gray-300 object-cover">
            <?php endif; ?>
            <a href="edit_profile.php" class="text-blue-500 hover:underline">Edit Profile</a>
        </div>

        <p class="text-gray-600"><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p class="text-gray-600"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p class="text-gray-600 mb-4"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Your Event History</h2>
        <?php if (count($events) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($events as $event): ?>
                    <li class="p-4 bg-gray-100 rounded-lg shadow-sm">
                        <strong class="text-gray-800"><?= htmlspecialchars($event['title']) ?></strong> 
                        <div class="text-gray-600"><?= htmlspecialchars($event['event_date']) ?>, <?= htmlspecialchars($event['event_time']) ?></div>
                        <div class="text-gray-500">at <?= htmlspecialchars($event['location']) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">No event registrations found.</p>
        <?php endif; ?>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
