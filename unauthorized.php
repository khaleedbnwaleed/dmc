<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access - Danmodi Students Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Access Denied</h1>
            <p class="text-gray-600 mb-6">
                You don't have permission to access this page. Please contact your administrator if you believe this is an error.
            </p>
            
            <div class="space-y-3">
                <a href="index.php" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors inline-block">
                    <i class="fas fa-home mr-2"></i>
                    Go to Homepage
                </a>
                
                <?php if (isLoggedIn()): ?>
                <a href="<?php echo hasRole(['admin', 'coordinator']) ? 'portal.php' : 'student-dashboard.php'; ?>" 
                   class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
                <?php else: ?>
                <a href="login.php" class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors inline-block">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
