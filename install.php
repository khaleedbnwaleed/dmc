<?php
// Database installation script
// Run this file once to set up the database

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'danmodi_portal';

echo "<h1>Danmodi Students Care Portal - Database Installation</h1>";

try {
    // Connect to MySQL server (without selecting database)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Connected to MySQL server successfully!</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Database '$database' created successfully!</p>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute schema file
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);
        
        // Split SQL statements and execute them
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*|\s*$)/', $statement)) {
                $pdo->exec($statement);
            }
        }
        
        echo "<p>✅ Database schema created successfully!</p>";
    } else {
        echo "<p>❌ Schema file not found: $schemaFile</p>";
    }
    
    // Read and execute seed data file
    $seedFile = __DIR__ . '/database/seed_data.sql';
    if (file_exists($seedFile)) {
        $seedData = file_get_contents($seedFile);
        
        // Split SQL statements and execute them
        $statements = array_filter(array_map('trim', explode(';', $seedData)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*|\s*$)/', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore duplicate entry errors for seed data
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        echo "<p>✅ Sample data inserted successfully!</p>";
    } else {
        echo "<p>❌ Seed data file not found: $seedFile</p>";
    }
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🎉 Installation Complete!</h3>";
    echo "<p><strong>Database Setup:</strong></p>";
    echo "<ul>";
    echo "<li>Database: <code>$database</code></li>";
    echo "<li>Tables: Created with sample data</li>";
    echo "<li>Demo Users: Ready to use</li>";
    echo "</ul>";
    
    echo "<p><strong>Demo Login Credentials:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@danmodi.gov.ng / admin123</li>";
    echo "<li><strong>Coordinator:</strong> coordinator@danmodi.gov.ng / coord123</li>";
    echo "<li><strong>Student:</strong> student@example.com / student123</li>";
    echo "</ul>";
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Delete this install.php file for security</li>";
    echo "<li>Update database credentials in config/database.php if needed</li>";
    echo "<li>Visit <a href='index.php'>index.php</a> to start using the portal</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Installation Failed!</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Please check:</strong></p>";
    echo "<ul>";
    echo "<li>MySQL server is running</li>";
    echo "<li>Database credentials are correct</li>";
    echo "<li>User has permission to create databases</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
}
h1 {
    color: #28a745;
    text-align: center;
    border-bottom: 2px solid #28a745;
    padding-bottom: 10px;
}
p {
    margin: 10px 0;
}
code {
    background: #e9ecef;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: monospace;
}
</style>
