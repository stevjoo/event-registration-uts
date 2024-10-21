<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = "";  

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['role'] == 'admin') {
            $message = "Admin users cannot be deleted.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $user_id]);

            if ($stmt->rowCount()) {
                $message = "User deleted successfully.";
            } else {
                $message = "Error deleting user.";
            }
        }
    } else {
        $message = "User not found.";
    }
} else {
    $message = "No user ID provided.";
}

$_SESSION['message'] = $message;

header("Location: view_users.php");
exit;
?>
