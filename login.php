<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // Get user from database
        $user = getUser($email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $error = 'Your account is not active. Please contact support.';
            } else {
                // Set session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'department' => $user['account_type']
                ];
                
                // Log activity
                logActivity($user['id'], 'login', 'User logged in successfully');
                
                // Redirect based on role
                if (in_array($user['role'], ['admin', 'coordinator'])) {
                    redirect('portal.php');
                } else {
                    redirect('student-dashboard.php');
                }
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}

// Get demo accounts for display
$demo_accounts = [
    [
        'email' => 'admin@danmodi.gov.ng',
        'password' => 'admin123',
        'role' => 'admin',
        'name' => 'Senior Special Assistant'
    ],
    [
        'email' => 'coordinator@danmodi.gov.ng', 
        'password' => 'coord123',
        'role' => 'coordinator',
        'name' => 'Program Coordinator'
    ],
    [
        'email' => 'student@example.com',
        'password' => 'student123', 
        'role' => 'student',
        'name' => 'Aisha Mohammed'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Danmodi Students Care Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-6">
        <!-- Header -->
        <div class="text-center my-6">
  <img src="logo.png" alt="Danmodi Students Care Logo" class="w-16 h-16 rounded-md mx-auto">
  <h1 class="text-2xl font-bold text-gray-900 mt-2">Portal Access</h1>
  <p class="text-gray-600">Danmodi Students Care - Jigawa State</p>
</div>


        

        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-lock mr-2"></i>
                    Sign In
                </h2>
                <p class="text-gray-600 text-sm">Enter your credentials to access the portal</p>
            </div>

            <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                    <span class="text-red-700 text-sm"><?php echo $error; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" id="email" name="email" required
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="your.email@example.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Enter your password">
                        <button type="button" onclick="togglePassword()" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-green-600 hover:underline">Forgot password?</a>
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="signup.php" class="text-green-600 hover:underline font-medium">Sign up here</a>
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="index.php" class="text-gray-600 hover:text-green-600 transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>
