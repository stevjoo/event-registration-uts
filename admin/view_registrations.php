<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->query("SELECT users.name, users.email, events.title FROM registrations JOIN users ON registrations.id = users.id JOIN events ON registrations.event_id = events.event_id");

$registrations = $stmt->fetchAll();

if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    ob_clean(); 

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment;filename=registrations.csv');
    
    $output = fopen('php://output', 'w');

    $delimiter = ';';

    fputcsv($output, ['UserName', 'Email', 'Event Title'], $delimiter);

    foreach ($registrations as $registration) {
        fputcsv($output, [
            $registration['name'],
            $registration['email'],
            $registration['title']
        ], $delimiter);
    }

    fclose($output);
    exit;
}
?>

<?php include '../includes/navbar.php'; ?>

<h1 class="text-center text-2xl font-bold my-6">View Event Registrations</h1>

<div class="overflow-x-auto mx-4 md:mx-24">
    <table class="w-full">
        <thead>
            <tr>    
                <th class="p-2 border">UserName</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Event Title</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($registrations) > 0): ?>
                <?php foreach ($registrations as $registration): ?>
                    <tr>
                        <td class="p-2 border"><?= htmlspecialchars($registration['name']) ?></td>
                        <td class="p-2 border"><?= htmlspecialchars($registration['email']) ?></td>
                        <td class="p-2 border"><?= htmlspecialchars($registration['title']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="p-2 border text-center">No registrations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="view_registrations.php?export=csv" class="flex mt-12 mx-auto justify-center bg-blue-500 text-white px-4 py-4 rounded-md shadow-md hover:bg-blue-600 transition duration-300 ease-in-out w-[120px]">
    Export CSV
</a>


<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    
    @media (max-width: 768px) {
        table {
            font-size: 0.9em;
        }
        th, td {
            padding: 8px;
        }
        .mx-24 {
            margin-left: 1rem;
            margin-right: 1rem;
        }
    }

    @media (max-width: 480px) {
        table {
            font-size: 0.8em;
        }
        th, td {
            padding: 5px;
        }
        .btn {
            padding: 2px 4px;
            font-size: 0.8em;
        }
    }
</style>
