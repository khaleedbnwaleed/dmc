<?php
require_once 'config.php';

$error = '';
$success = '';

// Get LGAs from database
$lgas = getAllLGAs();

if ($_POST) {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $lga = $_POST['lga'] ?? '';
    $accountType = $_POST['accountType'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $agreeToTerms = isset($_POST['agreeToTerms']);
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!$agreeToTerms) {
        $error = 'Please agree to the terms and conditions';
    } else {
        // Check if email already exists
        $existingUser = getUser($email);
        if ($existingUser) {
            $error = 'An account with this email already exists';
        } else {
            // Create new user
            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $accountType === 'student' ? 'student' : 'applicant',
                'lga' => $lga,
                'account_type' => $accountType
            ];
            
            $userId = createUser($userData);
            
            if ($userId) {
                // Auto-login the new user
                $_SESSION['user'] = [
                    'id' => $userId,
                    'email' => $email,
                    'role' => $userData['role'],
                    'name' => $firstName . ' ' . $lastName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'department' => $accountType === 'student' ? 'Current Beneficiary' : 'New Applicant'
                ];
                
                // Log activity
                logActivity($userId, 'register', 'New user account created');
                
                // Create welcome notification
                createNotification($userId, 'Welcome to Danmodi Students Care!', 
                    'Your account has been created successfully. You can now apply for scholarships and track your applications.');
                
                $success = 'Account created successfully! Redirecting...';
                
                // Redirect after 2 seconds
                if ($accountType === 'student') {
                    header('refresh:2;url=student-dashboard.php');
                } else {
                    header('refresh:2;url=apply.php');
                }
            } else {
                $error = 'Failed to create account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Danmodi Students Care Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-6">
        <!-- Header -->
        <div class="text-center">
            <div class="flex justify-center mb-6">
  <img src="logo.png" alt="Danmodi Students Care Logo" class="w-16 h-16 rounded-md">
</div>

            <h1 class="text-2xl font-bold text-gray-900">Create Account</h1>
            <p class="text-gray-600">Join Danmodi Students Care Portal</p>
        </div>

        <!-- Info Card -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                <i class="fas fa-graduation-cap text-green-600 mr-2"></i>
                Why Create an Account?
            </h3>
            <div class="text-xs text-green-700 space-y-1">
                <p>✓ Apply for scholarships and track your application</p>
                <p>✓ Save your progress and continue later</p>
                <p>✓ Receive important updates and notifications</p>
                <p>✓ Access your personalized dashboard</p>
            </div>
        </div>

        <!-- Signup Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user mr-2"></i>
                    Sign Up
                </h2>
                <p class="text-gray-600 text-sm">Create your account to access the portal and apply for scholarships</p>
            </div>

            <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                    <span class="text-red-700 text-sm"><?php echo $error; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    <span class="text-green-700 text-sm"><?php echo $success; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required
                               value="<?php echo $_POST['firstName'] ?? ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Enter first name">
                    </div>
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required
                               value="<?php echo $_POST['lastName'] ?? ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Enter last name">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" id="email" name="email" required
                               value="<?php echo $_POST['email'] ?? ''; ?>"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="your.email@example.com">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="tel" id="phone" name="phone" required
                               value="<?php echo $_POST['phone'] ?? ''; ?>"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="+234 xxx xxx xxxx">
                    </div>
                </div>

                <div>
                    <label for="lga" class="block text-sm font-medium text-gray-700 mb-1">Local Government Area *</label>
                    <select id="lga" name="lga" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select your LGA</option>
                        <?php foreach ($lgas as $lga): ?>
                        <option value="<?php echo htmlspecialchars($lga['name']); ?>" <?php echo ($_POST['lga'] ?? '') === $lga['name'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lga['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="accountType" class="block text-sm font-medium text-gray-700 mb-1">Account Type *</label>
                    <select id="accountType" name="accountType" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select account type</option>
                        <option value="applicant" <?php echo ($_POST['accountType'] ?? '') === 'applicant' ? 'selected' : ''; ?>>
                            New Applicant - Apply for Scholarship
                        </option>
                        <option value="student" <?php echo ($_POST['accountType'] ?? '') === 'student' ? 'selected' : ''; ?>>
                            Current Beneficiary - Access Dashboard
                        </option>
                        <option value="parent" <?php echo ($_POST['accountType'] ?? '') === 'parent' ? 'selected' : ''; ?>>
                            Parent/Guardian
                        </option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full pr-10 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Create a password (min. 6 characters)">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon1')" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                    <div class="relative">
                        <input type="password" id="confirmPassword" name="confirmPassword" required
                               class="w-full pr-10 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Confirm your password">
                        <button type="button" onclick="togglePassword('confirmPassword', 'toggleIcon2')" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="agreeToTerms" name="agreeToTerms" 
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <label for="agreeToTerms" class="ml-2 text-sm text-gray-600">
                        I agree to the 
                        <a href="#" class="text-green-600 hover:underline">Terms and Conditions</a>
                        and 
                        <a href="#" class="text-green-600 hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                    Create Account & Continue
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-green-600 hover:underline font-medium">Sign in here</a>
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
        function togglePassword(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(iconId);
            
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
