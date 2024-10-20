<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<?php include '../includes/navbar.php'; ?>

<h1 class="text-center text-2xl font-bold my-6">View Users</h1>

<div class="overflow-x-auto mx-4 md:mx-24">
    <table class="w-full">
        <thead>
            <tr>
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Role</th>
                <th class="p-2 border">Registration Date</th>
                <th class="p-2 border">Action</th> 
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="p-2 border"><?= htmlspecialchars($user['id']) ?></td>
                        <td class="p-2 border"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="p-2 border"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="p-2 border"><?= htmlspecialchars($user['role']) ?></td>
                        <td class="p-2 border"><?= htmlspecialchars($user['created_at']) ?></td>
                        <td class="p-2 border">
                            <?php if ($user['role'] != 'admin'): ?>
                                <button class="btn_delete" onclick="confirmDeleteUser(<?= $user['id'] ?>)">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="p-2 border text-center">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function confirmDeleteUser(userId) {
        const confirmDelete = confirm('Are you sure you want to delete this user? This action cannot be undone.');
        if (confirmDelete) {
            deleteUser(userId);
        }
    }

    function deleteUser(userId) {
        fetch(`delete_user.php?id=${userId}`, {
            method: 'POST',
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete user');
            }
            return response.text();
        })
        .then(result => {
            alert('User deleted successfully!');
            location.reload();  
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            alert('An error occurred while trying to delete the user.');
        });
    }
</script>

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
    .btn_delete {
        padding: 5px 10px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    .btn_delete:hover {
        background-color: #c82333;
    }
    
    /* Responsive Styles */
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
        .btn_delete {
            padding: 3px 7px;
            font-size: 0.8em;
        }
    }
</style>
