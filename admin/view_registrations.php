<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event Registrations</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        h1 {
            margin: 20px 0;
            color: #343a40; 
        }
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
    </style>
</head>
<body>

<?php
session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

include '../includes/navbar.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=event_management', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT u.name, u.email, e.title 
        FROM registrations r
        JOIN users u ON r.id = u.id
        JOIN events e ON r.event_id = e.event_id
        ORDER BY r.registered_at DESC
    ");
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $registrations = []; 
}

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

<body class="bg-gradient-to-r from-green-200 to-blue-200">
<div class="container mt-5">
    <h1 class="text-center text-2xl font-bold my-6">View Event Registrations</h1>

    <!-- Export CSV Button -->
    <a href="view_registrations.php?export=csv" class="btn btn-primary mb-3">Export CSV</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>UserName</th>
                    <th>Email</th>
                    <th>Event Title</th>
                </tr>
            </thead>
            <tbody>

            <?php if (isset($registrations) && count($registrations) > 0): ?>
                    <?php foreach ($registrations as $registration): ?>
                        <tr>
                            <td><?= htmlspecialchars($registration['name']) ?></td>
                            <td><?= htmlspecialchars($registration['email']) ?></td>
                            <td><?= htmlspecialchars($registration['title']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No registrations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
<!-- Include Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
