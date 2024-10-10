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
    SELECT e.event_id, e.title, e.event_date, e.event_time, e.location 
    FROM registrations er 
    JOIN events e ON er.event_id = e.event_id 
    WHERE er.id = :user_id
    ORDER BY e.event_date DESC
");
$stmt->execute(['user_id' => $user_id]);
$events = $stmt->fetchAll();

if (isset($_POST['cancel_event_id'])) {
    $cancel_event_id = $_POST['cancel_event_id'];
    
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = :user_id AND event_id = :event_id");
    $stmt->execute(['user_id' => $user_id, 'event_id' => $cancel_event_id]);
    
    header("Location: registered_event.php");
    exit;
}
?>

<?php include '../includes/navbar.php'; ?>

<div class="event-history">
    <h1 class="text-center text-2xl font-bold my-6">Your Event History</h1>
    <?php if (count($events) > 0): ?>
<ul class="space-y-4">
    <?php foreach ($events as $event): ?>
        <li class="mx-16 px-4 py-2 pt-4 bg-slate-100 shadow-md rounded-lg flex flex-col justify-between">
            <div>
                <strong class="text-3xl text-gray-800"><?= htmlspecialchars($event['title']) ?></strong> 
                <p class="text-md text-gray-500">
                    <?= htmlspecialchars($event['event_date']) ?>
                </p>
                <p class="text-md text-gray-500">
                    <?= htmlspecialchars($event['event_time']) ?>
                    at <?= htmlspecialchars($event['location']) ?>
                </p>
            </div>
            <form method="POST" action="registered_event.php" class="mt-4 self-end md:self-start">
                <input type="hidden" name="cancel_event_id" value="<?= htmlspecialchars($event['event_id']) ?>">
                <button type="submit" class="px-4 py-2 bg-red-500 text-white font-semibold rounded hover:bg-red-600 transition">
                    Cancel Registration
                </button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
    <p class="text-gray-500 text-center">No event registrations found.</p>
<?php endif; ?>

</div>

<style>
    .event-history ul {
        list-style-type: none;
        padding: 0;
    }
    .event-history li {
        margin-bottom: 10px;
    }
    .btn-danger {
        background-color: #d9534f;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
    .btn-danger:hover {
        background-color: #c9302c;
    }
</style>
