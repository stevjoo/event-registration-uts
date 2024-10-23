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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone']; 
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^08\d{8,10}$/', $phone)) {
        $errors[] = 'Invalid phone number format. It should start with 08 followed by 8-10 digits.';
    }
    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match';
        }
    }

    $avatar_path = $user['avatar']; 
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
        $avatar_name = $_FILES['avatar']['name'];
        $avatar_ext = pathinfo($avatar_name, PATHINFO_EXTENSION);
        $avatar_new_name = "avatar_" . $user_id . "." . $avatar_ext;
        $avatar_upload_dir = '../uploads/avatars/';
        $avatar_upload_path = $avatar_upload_dir . $avatar_new_name;

        if (!is_dir($avatar_upload_dir)) {
            mkdir($avatar_upload_dir, 0755, true);
        }

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($avatar_ext), $allowed_extensions)) {
            if (move_uploaded_file($avatar_tmp_name, $avatar_upload_path)) {
                $avatar_path = $avatar_new_name;
            } else {
                $errors[] = 'Failed to upload avatar image.';
            }
        } else {
            $errors[] = 'Invalid image type. Only JPG, PNG, and GIF are allowed.';
        }
    }

    if (count($errors) === 0) {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, avatar = :avatar WHERE id = :id");
        $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'avatar' => $avatar_path, 'id' => $user_id]);

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashed_password, 'id' => $user_id]);
        }

        header("Location: view_profile.php");
        exit;
    }
}
?>

<?php include '../includes/navbar.php'; ?>

<body class="bg-gradient-to-r from-blue-200 to-purple-200">
    <div class="container mx-auto my-10">
        <h1 class="text-center text-2xl font-bold my-6">Edit Profile</h1>
        
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="bg-white/50 p-6 rounded-lg shadow-lg">
            
            <div class="mb-4 text-center">
                <div class="mb-2 relative inline-block">
                    <?php if ($user['avatar']): ?>
                        <img src="../uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover">
                        <?php else: ?>
                            <img src="../uploads/avatars/default-avatar.png" alt="Default Avatar" class="w-32 h-32 rounded-full object-cover">
                            <?php endif; ?>
                            
                            <label for="avatar" class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-md cursor-pointer hover:bg-gray-200">
                                <i class="fa fa-pencil-alt text-gray-700"></i>
                                <input type="file" id="avatar" name="avatar" class="hidden">
                </label>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="w-full bg-white/75 p-2 border border-gray-300 rounded-md">
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full bg-white/75 p-2 border border-gray-300 rounded-md">
        </div>
        
        <div class="mb-4">
            <label for="phone" class="block text-gray-700 font-bold mb-2">Phone Number:</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="08XXXXXXXXXX" class="w-full bg-white/75 p-2 border border-gray-300 rounded-md">
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-bold mb-2">New Password (optional):</label>
            <input type="password" id="password" name="password" class="w-full bg-white/75 p-2 border border-gray-300 rounded-md">
        </div>
        
        <div class="mb-4">
            <label for="password_confirm" class="block text-gray-700 font-bold mb-2">Confirm New Password:</label>
            <input type="password" id="password_confirm" name="password_confirm" class="w-full bg-white/75 p-2 border border-gray-300 rounded-md">
        </div>
        
        <?php if (count($errors) > 0): ?>
            <ul class="mb-4">
                <?php foreach ($errors as $error): ?>
                    <li class="text-red-500"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                
        <div class="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition w-full text-sm sm:w-auto">Update Profile</button>
            <a href="view_profile.php" class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 transition w-full sm:w-auto text-center">Cancel</a>
        </div>
        
    </form>
</div>
</body>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
