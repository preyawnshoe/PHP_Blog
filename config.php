<?php
/**
 * Database Configuration
 * 
 * This file contains all the database configuration settings.
 * Update these values according to your environment.
 */

// Database Configuration
define('DB_HOST', 'sql213.infinityfree.com');
define('DB_USER', 'if0_39679920');
define('DB_PASSWORD', 'Lonewolf2112');
define('DB_NAME', 'if0_39679920_blog_db');
define('DB_CHARSET', 'utf8mb4');

// Database Connection Options
define('DB_OPTIONS', [
    MYSQLI_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
    MYSQLI_OPT_CONNECT_TIMEOUT => 5,
    MYSQLI_OPT_READ_TIMEOUT => 5
]);

// Application Configuration
define('APP_NAME', 'PHP Blog');
define('APP_VERSION', '1.0.0');

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 6);
define('USERNAME_MIN_LENGTH', 3);

// Environment Configuration
// Set to 'development' for debugging, 'production' for live site
define('ENVIRONMENT', 'development');

// Error Reporting (based on environment)
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Timezone
date_default_timezone_set('UTC');
?>
