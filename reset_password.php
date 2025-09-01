<?php
require_once 'db.php';

$db = new Database();
$pdo = $db->getConnection();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $manual_password = $_POST['manual_password'];
    
    // Check if user exists and is admin
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND email = ? AND role = 'admin'");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Reset password to manual password
        $newPassword = password_hash($manual_password, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($updateStmt->execute([$newPassword, $user['id']])) {
            $message = 'Password has been reset. Please login with the new password.';
        } else {
            $message = 'Error resetting password.';
        }
    } else {
        $message = 'User not found or not an admin.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Reset Password</h1>
        
        <?php if ($message): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                <input type="text" id="username" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label for="manual_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" id="manual_password" name="manual_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium mb-4">Reset Password</button>
        </form>
        
        <p class="text-center"><a href="login.php" class="text-blue-600 hover:text-blue-900">Back to Login</a></p>
    </div>
</body>
</html>
