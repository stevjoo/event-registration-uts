<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../view_events.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT e.title, e.event_date, e.event_time, e.location 
    FROM registrations er  -- Use correct table name
    JOIN events e ON er.event_id = e.event_id 
    WHERE er.id = :user_id  -- Correct column reference
    ORDER BY e.event_date DESC
");
$stmt->execute(['user_id' => $user_id]);
$events = $stmt->fetchAll();
?>

<?php include '../includes/navbar.php'; ?>

<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-center text-3xl font-bold text-gray-800 mb-8">Your Profile</h1>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Personal Information</h2>
        <p class="text-gray-600"><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p class="text-gray-600 mb-4"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <a href="edit_profile.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">Edit Profile</a>
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
