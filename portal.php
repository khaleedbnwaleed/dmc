<?php
require_once 'config.php';

// Require admin or coordinator role
requireRole(['admin', 'coordinator']);

// Get dashboard statistics
$stats = getApplicationStats();

// Get recent applications
global $db;
$recentApplications = $db->fetchAll("
    SELECT a.*, u.first_name, u.last_name, l.name as lga_name, sf.name as study_field_name, i.name as institution_name
    FROM applications a
    JOIN users u ON a.user_id = u.id
    LEFT JOIN lgas l ON a.lga_id = l.id
    LEFT JOIN study_fields sf ON a.study_field_id = sf.id
    LEFT JOIN institutions i ON a.preferred_institution_id = i.id
    ORDER BY a.created_at DESC
    LIMIT 10
");

// Get LGA performance data
$lgaStats = $db->fetchAll("
    SELECT l.name, 
           COUNT(a.id) as total_applications,
           COUNT(CASE WHEN a.status = 'approved' THEN 1 END) as approved_applications,
           COUNT(s.id) as active_scholarships
    FROM lgas l
    LEFT JOIN applications a ON l.id = a.lga_id
    LEFT JOIN scholarships s ON a.id = s.application_id AND s.status = 'active'
    GROUP BY l.id, l.name
    HAVING total_applications > 0
    ORDER BY approved_applications DESC, total_applications DESC
    LIMIT 10
");

// Get monthly application trends
$monthlyTrends = $db->fetchAll("
    SELECT DATE_FORMAT(submitted_at, '%Y-%m') as month,
           COUNT(*) as applications,
           COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved
    FROM applications 
    WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(submitted_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Danmodi Students Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <img src="https://via.placeholder.com/40x40/16a34a/ffffff?text=Logo" 
                         alt="Logo" class="w-10 h-10 rounded-lg">
                    <div>
                        <h1 class="text-xl font-bold text-green-700">Danmodi Students Care</h1>
                        <p class="text-xs text-gray-600">Management Portal</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-sm text-gray-600 hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Website
                    </a>
                    
                    <div class="relative">
                        <button class="relative p-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                <?php echo $stats['pending_applications']; ?>
                            </span>
                        </button>
                    </div>

                    <div class="flex items-center space-x-2">
                        <img src="https://via.placeholder.com/32x32/6b7280/ffffff?text=<?php echo substr($_SESSION['user']['name'], 0, 1); ?>" 
                             alt="User" class="w-8 h-8 rounded-full">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></div>
                            <div class="text-gray-500"><?php echo ucfirst($_SESSION['user']['role']); ?></div>
                        </div>
                        <a href="logout.php" class="ml-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 text-white mb-8">
            <h2 class="text-2xl font-bold mb-2">Welcome to Danmodi Students Care Portal</h2>
            <p class="text-green-100">
                Empowering underprivileged and rural students across all 27 LGAs of Jigawa State
            </p>
            <div class="mt-4 flex items-center space-x-4 text-sm">
                <span><i class="fas fa-map-marker-alt mr-1"></i> Dutse, Jigawa State</span>
                <span><i class="fas fa-globe mr-1"></i> danmodistudentscare.com.ng</span>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Applications</p>
                        <p class="text-2xl font-bold text-green-600"><?php echo number_format($stats['total_applications']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-green-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <span class="text-green-600">+<?php echo $stats['pending_applications']; ?></span> pending review
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Scholarships</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo number_format($stats['active_scholarships']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Across undergraduate & postgraduate</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-purple-600"><?php echo number_format($stats['total_users']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Registered accounts</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Funds Disbursed</p>
                        <p class="text-2xl font-bold text-orange-600"><?php echo formatCurrency($stats['total_disbursed']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-naira-sign text-orange-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Total payments made</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Applications -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap text-green-500 mr-2"></i>
                        Recent Applications
                    </h3>
                    <p class="text-sm text-gray-600">Latest scholarship applications</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach (array_slice($recentApplications, 0, 5) as $app): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">
                                    <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    <?php echo htmlspecialchars($app['study_field_name'] ?? 'N/A'); ?> • 
                                    <?php echo htmlspecialchars($app['lga_name'] ?? 'N/A'); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Applied: <?php echo formatDate($app['created_at']); ?>
                                </p>
                            </div>
                            <div class="ml-4">
                                <?php
                                $statusColors = [
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
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4">
                        <a href="applications.php" class="w-full bg-transparent border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors text-center block">
                            View All Applications
                        </a>
                    </div>
                </div>
            </div>

            <!-- Top Performing LGAs -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top Performing LGAs</h3>
                    <p class="text-sm text-gray-600">Application and approval statistics</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach (array_slice($lgaStats, 0, 5) as $index => $lga): ?>
                        <?php
                        $successRate = $lga['total_applications'] > 0 ? 
                            round(($lga['approved_applications'] / $lga['total_applications']) * 100) : 0;
                        ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 font-semibold text-sm"><?php echo $index + 1; ?></span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($lga['name']); ?></p>
                                    <p class="text-sm text-gray-600">
                                        <?php echo $lga['total_applications']; ?> applications • 
                                        <?php echo $successRate; ?>% success rate
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">
                                    <?php echo $lga['active_scholarships']; ?> active
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm mt-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                <p class="text-sm text-gray-600">Common administrative tasks</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="applications.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-alt text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">Review Applications</span>
                    </a>
                    
                    <a href="users.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-users text-2xl text-blue-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">Manage Users</span>
                    </a>
                    
                    <a href="payments.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-naira-sign text-2xl text-purple-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">Payment Records</span>
                    </a>
                    
                    <a href="reports.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chart-bar text-2xl text-orange-600 mb-2"></i>
                        <span class="text-sm font-medium text-gray-900">Generate Reports</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
