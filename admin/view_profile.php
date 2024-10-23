<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, email, phone, avatar FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();
?>

<?php include '../includes/navbar.php'; ?>

<body class="bg-gradient-to-r from-green-200 to-blue-200 text-white">
    <div class="container mx-auto my-10">
        <h1 class="text-center text-2xl font-bold my-6 text-black">Your Profile</h1>
        
        <div class="bg-white/75 shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-black mb-4">Personal Information</h2>
            
            <div class="flex items-center space-x-4 mb-4">
                <?php
            if (!empty($user['avatar'])):
                $avatar_path = '../uploads/avatars/' . htmlspecialchars($user['avatar']);
                if (file_exists($avatar_path)): 
                    ?>
                    <img src="<?= $avatar_path ?>" alt="Profile Avatar" class="w-24 h-24 rounded-full border border-gray-300 object-cover">
                    <?php else: ?>
                        <img src="../uploads/avatars/default-avatar.png" alt="Profile Avatar" class="w-24 h-24 rounded-full border border-gray-300 object-cover">
                        <?php endif; ?>
                        <?php else: ?>
                            <img src="../uploads/avatars/default-avatar.png" alt="Profile Avatar" class="w-24 h-24 rounded-full border border-gray-300 object-cover">
                            <?php endif; ?>
                            <a href="edit_profile.php" class="text-blue-500 hover:underline">Edit Profile</a>
                        </div>
                        
                        <p class="text-black"><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                        <p class="text-black"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p class="text-black mb-4"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                    </div>
</div>
</body>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
