<?php
require_once 'config.php';

// Require student role
requireRole(['student']);

$userId = $_SESSION['user']['id'];

// Get user's applications
$applications = getUserApplications($userId);

// Get user's scholarships
global $db;
$scholarships = $db->fetchAll("
    SELECT s.*, a.application_number, sf.name as study_field_name, i.name as institution_name
    FROM scholarships s
    JOIN applications a ON s.application_id = a.id
    LEFT JOIN study_fields sf ON a.study_field_id = sf.id
    LEFT JOIN institutions i ON a.preferred_institution_id = i.id
    WHERE s.user_id = ?
    ORDER BY s.created_at DESC
", [$userId]);

// Get recent payments
$recentPayments = $db->fetchAll("
    SELECT p.*, s.scholarship_type
    FROM payments p
    JOIN scholarships s ON p.scholarship_id = s.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
    LIMIT 5
", [$userId]);

// Get notifications
$notifications = getUserNotifications($userId, 5);

// Calculate statistics
$totalReceived = $db->fetch("
    SELECT SUM(amount) as total 
    FROM payments 
    WHERE user_id = ? AND status = 'completed'
", [$userId])['total'] ?? 0;

$activeScholarships = count(array_filter($scholarships, function($s) {
    return $s['status'] === 'active';
}));

// Get current academic info (from most recent application)
$currentApp = !empty($applications) ? $applications[0] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Danmodi Students Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <img src="logo.png" width="60" height="60" class="rounded-md" alt="Logo">
                    <div>
                        <h1 class="text-xl font-bold text-green-700">Student Portal</h1>
                        <p class="text-sm text-gray-600">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['first_name']); ?></p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-sm text-gray-600 hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Website
                    </a>
                    
                    <div class="relative">
                        <button class="relative p-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-bell text-lg"></i>
                            <?php if (count($notifications) > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                <?php echo count($notifications); ?>
                            </span>
                            <?php endif; ?>
                        </button>
                    </div>
                    
                    <a href="logout.php" class="text-gray-600 hover:text-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 text-white mb-8">
            <h2 class="text-2xl font-bold mb-2">Welcome to Your Dashboard</h2>
            <p class="text-green-100">
                Track your scholarship status, academic progress, and stay updated with program announcements.
            </p>
            <div class="mt-4 flex items-center space-x-4 text-sm">
                <span><i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($_SESSION['user']['department'] ?? 'Beneficiary'); ?></span>
                <?php if ($activeScholarships > 0): ?>
                <span><i class="fas fa-graduation-cap mr-1"></i> Active Scholarship</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Scholarship Status</p>
                        <p class="text-2xl font-bold <?php echo $activeScholarships > 0 ? 'text-green-600' : 'text-gray-400'; ?>">
                            <?php echo $activeScholarships > 0 ? 'Active' : 'None'; ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-award text-green-600 text-xl"></i>
                    </div>
                </div>
                <?php if ($activeScholarships > 0): ?>
                <p class="text-xs text-gray-500 mt-2">
                    <span class="text-green-600">Approved</span> for 2024/2025
                </p>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Applications</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo count($applications); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Total submitted</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Funds Received</p>
                        <p class="text-2xl font-bold text-purple-600"><?php echo formatCurrency($totalReceived); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-naira-sign text-purple-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Total received</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Study Field</p>
                        <p class="text-2xl font-bold text-orange-600">
                            <?php echo $currentApp ? htmlspecialchars($currentApp['study_field_name']) : 'N/A'; ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-orange-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <?php echo $currentApp ? htmlspecialchars($currentApp['course_of_study']) : 'Not specified'; ?>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Applications Status -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap text-green-500 mr-2"></i>
                        My Applications
                    </h3>
                    <p class="text-sm text-gray-600">Your scholarship applications and status</p>
                </div>
                <div class="p-6">
                    <?php if (empty($applications)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600 mb-4">No applications found</p>
                        <a href="apply.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Apply for Scholarship
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($applications as $app): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">
                                    Application #<?php echo htmlspecialchars($app['application_number']); ?>
                                </h4>
                                <?php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'under_review' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800'
                                ];
                                $statusColor = $statusColors[$app['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $statusColor; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>Field:</strong> <?php echo htmlspecialchars($app['study_field_name'] ?? 'N/A'); ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>Institution:</strong> <?php echo htmlspecialchars($app['institution_name'] ?? 'N/A'); ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                Submitted: <?php echo $app['submitted_at'] ? formatDate($app['submitted_at']) : 'Not submitted'; ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Activities & Notifications -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bell text-blue-500 mr-2"></i>
                        Recent Notifications
                    </h3>
                    <p class="text-sm text-gray-600">Latest updates and announcements</p>
                </div>
                <div class="p-6">
                    <?php if (empty($notifications)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">No notifications yet</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($notifications as $notification): ?>
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($notification['title']); ?>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?php echo formatDateTime($notification['created_at']); ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <?php if (!empty($recentPayments)): ?>
        <div class="bg-white rounded-lg shadow-sm mt-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                <p class="text-sm text-gray-600">Your payment history</p>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentPayments as $payment): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo formatDate($payment['payment_date'] ?? $payment['created_at']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo ucfirst($payment['payment_type']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo formatCurrency($payment['amount']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusColor = $statusColors[$payment['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $statusColor; ?>">
                                        <?php echo ucfirst($payment['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm mt-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                <p class="text-sm text-gray-600">Common tasks and important links</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="apply.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-alt text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">New Application</span>
                    </a>
                    
                    <a href="profile.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user text-2xl text-blue-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">Update Profile</span>
                    </a>
                    
                    <a href="payments.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-naira-sign text-2xl text-purple-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">Payment History</span>
                    </a>
                    
                    <a href="support.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-question-circle text-2xl text-orange-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">Get Support</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
