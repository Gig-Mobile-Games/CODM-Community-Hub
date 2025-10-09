<?php
// CODM Community Hub Installation Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Database configuration
$db_config = [
    'host' => 'localhost',
    'name' => 'codm_community_hub',
    'username' => 'root',
    'password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 1) {
    $db_config['host'] = $_POST['db_host'] ?? 'localhost';
    $db_config['name'] = $_POST['db_name'] ?? 'codm_community_hub';
    $db_config['username'] = $_POST['db_username'] ?? 'root';
    $db_config['password'] = $_POST['db_password'] ?? '';
    
    // Test database connection
    try {
        $pdo = new PDO(
            "mysql:host={$db_config['host']};charset=utf8mb4",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_config['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$db_config['name']}`");
        
        // Read and execute schema
        $schema = file_get_contents('database/schema.sql');
        $statements = explode(';', $schema);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        // Update database configuration
        $config_content = "<?php
class Database {
    private \$host = '{$db_config['host']}';
    private \$db_name = '{$db_config['name']}';
    private \$username = '{$db_config['username']}';
    private \$password = '{$db_config['password']}';
    private \$conn;

    public function getConnection() {
        \$this->conn = null;
        
        try {
            \$this->conn = new PDO(
                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name . \";charset=utf8mb4\",
                \$this->username,
                \$this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException \$exception) {
            echo \"Connection error: \" . \$exception->getMessage();
        }
        
        return \$this->conn;
    }
}
?>";
        
        file_put_contents('config/database.php', $config_content);
        
        $success = 'Database setup completed successfully!';
        $step = 2;
        
    } catch (Exception $e) {
        $error = 'Database setup failed: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
    $admin_username = $_POST['admin_username'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';
    
    if (empty($admin_username) || empty($admin_email) || empty($admin_password)) {
        $error = 'Please fill in all admin account fields';
    } else {
        try {
            require_once 'includes/auth.php';
            $auth = new Auth();
            
            $result = $auth->register($admin_username, $admin_email, $admin_password, 'admin');
            
            if ($result['success']) {
                $success = 'Admin account created successfully! Installation complete.';
                $step = 3;
            } else {
                $error = $result['message'];
            }
        } catch (Exception $e) {
            $error = 'Failed to create admin account: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODM Community Hub - Installation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .install-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .step {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
        }
        .step-number {
            background: var(--accent);
            color: var(--dark);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        .step-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="text-center mb-30">
            <div class="logo" style="justify-content: center; margin-bottom: 20px;">
                <i class="fas fa-crosshairs"></i>
                <span>CODM HUB</span>
            </div>
            <h1>Installation</h1>
            <p>Welcome to CODM Community Hub installation wizard</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
        <div class="step">
            <div class="step-header">
                <div class="step-number">1</div>
                <h2>Database Configuration</h2>
            </div>
            <p>Please provide your MySQL database credentials:</p>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Database Host</label>
                        <input type="text" name="db_host" class="form-control" value="<?php echo htmlspecialchars($db_config['host']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Database Name</label>
                        <input type="text" name="db_name" class="form-control" value="<?php echo htmlspecialchars($db_config['name']); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Database Username</label>
                        <input type="text" name="db_username" class="form-control" value="<?php echo htmlspecialchars($db_config['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Database Password</label>
                        <input type="password" name="db_password" class="form-control" value="<?php echo htmlspecialchars($db_config['password']); ?>">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-database"></i> Setup Database
                </button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($step == 2): ?>
        <div class="step">
            <div class="step-header">
                <div class="step-number">2</div>
                <h2>Admin Account</h2>
            </div>
            <p>Create your administrator account:</p>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Admin Username</label>
                    <input type="text" name="admin_username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Admin Email</label>
                    <input type="email" name="admin_email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Admin Password</label>
                    <input type="password" name="admin_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-user-shield"></i> Create Admin Account
                </button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($step == 3): ?>
        <div class="step">
            <div class="step-header">
                <div class="step-number">3</div>
                <h2>Installation Complete!</h2>
            </div>
            <p>Your CODM Community Hub has been successfully installed!</p>
            
            <div class="text-center mt-30">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Go to Website
                </a>
                <a href="admin/" class="btn btn-secondary">
                    <i class="fas fa-cog"></i> Admin Dashboard
                </a>
            </div>
            
            <div class="alert alert-info mt-20">
                <strong>Important:</strong> Please delete the install.php file for security reasons.
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
