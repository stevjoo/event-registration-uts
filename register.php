<?php
require 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; 

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, :role)");
    $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone ,'password' => $password, 'role' => $role]);

    header("Location: login.php");
    exit(); 
}
?>


<form method="POST" action="">
    <label>Name:</label>
    <input type="text" name="name" required>
    
    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Phone Number:</label>
    <input type="phone" name="phone" placeholder="08XXXXXXXXXX" required>
    
    <label>Password:</label>
    <input type="password" name="password" required>
    
    <button type="submit">Register</button>
</form>
