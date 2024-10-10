<?php
session_start();
require 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'admin')");
    $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);


    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <link rel="stylesheet" href="path/to/your/css/style.css"> 
</head>
<body>
    <h2>Register Admin</h2>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Register Admin</button>
    </form>
</body>
</html>
