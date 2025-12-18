<?php
/**
 * FusionMix Platform Configuration File
 * 
 * This file contains all customizable settings for the FusionMix database entry system.
 * Modify the values below to match your environment and requirements.
 * 
 * Created: 2025-12-18 08:18:27 UTC
 */

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================

define('DB_HOST', 'localhost');           // Database host address
define('DB_PORT', 3306);                  // Database port (default: 3306 for MySQL)
define('DB_USER', 'root');                // Database username
define('DB_PASSWORD', '');                // Database password
define('DB_NAME', 'fusionmix_db');        // Database name
define('DB_CHARSET', 'utf8mb4');          // Database character set

// Database connection options
define('DB_POOL_SIZE', 10);               // Connection pool size
define('DB_TIMEOUT', 30);                 // Connection timeout in seconds
define('DB_SSL_ENABLED', false);          // Enable SSL for database connection

// ============================================================================
// APPLICATION CONFIGURATION
// ============================================================================

define('APP_NAME', 'FusionMix');          // Application name
define('APP_VERSION', '1.0.0');           // Application version
define('APP_ENV', 'development');         // Environment: development, staging, production
define('APP_DEBUG', true);                // Enable debug mode (disable in production)
define('APP_TIMEZONE', 'UTC');            // Application timezone

// ============================================================================
// SECURITY CONFIGURATION
// ============================================================================

define('SECRET_KEY', 'your-secret-key-here-change-in-production'); // Encryption key
define('API_KEY', 'your-api-key-here');   // API authentication key
define('JWT_SECRET', 'your-jwt-secret-key-here'); // JWT secret for token generation
define('JWT_ALGORITHM', 'HS256');         // JWT algorithm (HS256, RS256, etc.)
define('JWT_EXPIRATION', 86400);          // JWT token expiration time in seconds (24 hours)

// CORS Configuration
define('CORS_ENABLED', true);             // Enable CORS
define('CORS_ALLOWED_ORIGINS', [          // Allowed origins for CORS
    'http://localhost:3000',
    'http://localhost:8080',
    'https://fusionmix.example.com'
]);
define('CORS_ALLOWED_METHODS', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
define('CORS_ALLOWED_HEADERS', 'Content-Type, Authorization, X-Requested-With');

// ============================================================================
// EMAIL CONFIGURATION
// ============================================================================

define('MAIL_DRIVER', 'smtp');            // Mail driver: smtp, sendmail, log
define('MAIL_HOST', 'smtp.mailtrap.io');  // SMTP host
define('MAIL_PORT', 587);                 // SMTP port
define('MAIL_USERNAME', '');              // SMTP username
define('MAIL_PASSWORD', '');              // SMTP password
define('MAIL_ENCRYPTION', 'tls');         // Encryption: tls, ssl
define('MAIL_FROM_ADDRESS', 'noreply@fusionmix.com'); // From email address
define('MAIL_FROM_NAME', 'FusionMix');    // From name

// ============================================================================
// LOGGING CONFIGURATION
// ============================================================================

define('LOG_CHANNEL', 'stack');           // Log channel: single, daily, slack, syslog, stack
define('LOG_LEVEL', 'debug');             // Log level: debug, info, notice, warning, error, critical, alert, emergency
define('LOG_PATH', __DIR__ . '/logs');    // Path to log files
define('LOG_MAX_FILES', 14);              // Maximum number of log files to keep
define('LOG_MAX_SIZE', 10485760);         // Maximum log file size in bytes (10MB)

// ============================================================================
// CACHE CONFIGURATION
// ============================================================================

define('CACHE_DRIVER', 'file');           // Cache driver: file, redis, memcached, database
define('CACHE_TTL', 3600);                // Cache time-to-live in seconds (1 hour)
define('CACHE_PATH', __DIR__ . '/storage/cache'); // Cache storage path

// Redis Cache Configuration
define('REDIS_HOST', 'localhost');        // Redis host
define('REDIS_PORT', 6379);               // Redis port
define('REDIS_PASSWORD', '');             // Redis password
define('REDIS_DATABASE', 0);              // Redis database number

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================

define('SESSION_DRIVER', 'file');         // Session driver: file, database, redis
define('SESSION_LIFETIME', 120);          // Session lifetime in minutes
define('SESSION_PATH', __DIR__ . '/storage/sessions'); // Session storage path
define('REMEMBER_ME_DURATION', 604800);   // Remember me duration in seconds (7 days)

// ============================================================================
// FILE UPLOAD CONFIGURATION
// ============================================================================

define('UPLOAD_DIR', __DIR__ . '/uploads'); // Upload directory
define('MAX_FILE_SIZE', 10485760);        // Maximum file size in bytes (10MB)
define('ALLOWED_EXTENSIONS', [            // Allowed file extensions
    'jpg', 'jpeg', 'png', 'gif', 'pdf',
    'doc', 'docx', 'xls', 'xlsx', 'csv',
    'txt', 'zip', 'rar'
]);
define('ALLOWED_MIME_TYPES', [            // Allowed MIME types
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'text/csv',
    'text/plain'
]);

// ============================================================================
// PAGINATION CONFIGURATION
// ============================================================================

define('ITEMS_PER_PAGE', 20);             // Default items per page
define('MAX_ITEMS_PER_PAGE', 100);        // Maximum items per page
define('PAGINATION_RANGE', 5);            // Number of pagination links to show

// ============================================================================
// API RATE LIMITING
// ============================================================================

define('RATE_LIMIT_ENABLED', true);       // Enable rate limiting
define('RATE_LIMIT_REQUESTS', 100);       // Number of requests allowed
define('RATE_LIMIT_WINDOW', 3600);        // Time window in seconds (1 hour)
define('RATE_LIMIT_MESSAGE', 'Too many requests. Please try again later.');

// ============================================================================
// FEATURE FLAGS
// ============================================================================

define('FEATURE_ADVANCED_ANALYTICS', true);  // Enable advanced analytics
define('FEATURE_EXPORT_DATA', true);         // Enable data export functionality
define('FEATURE_BULK_OPERATIONS', true);     // Enable bulk operations
define('FEATURE_API_ACCESS', true);          // Enable API access
define('FEATURE_NOTIFICATIONS', true);       // Enable user notifications
define('FEATURE_TWO_FACTOR_AUTH', false);    // Enable 2FA (set to true when implemented)

// ============================================================================
// THIRD-PARTY SERVICES
// ============================================================================

define('GOOGLE_CLIENT_ID', '');           // Google OAuth Client ID
define('GOOGLE_CLIENT_SECRET', '');       // Google OAuth Client Secret

define('AWS_ACCESS_KEY', '');             // AWS Access Key
define('AWS_SECRET_KEY', '');             // AWS Secret Key
define('AWS_REGION', 'us-east-1');        // AWS Region
define('AWS_S3_BUCKET', '');              // AWS S3 Bucket name

// ============================================================================
// MONITORING AND ANALYTICS
// ============================================================================

define('ANALYTICS_ENABLED', true);        // Enable analytics tracking
define('ERROR_TRACKING_ENABLED', true);   // Enable error tracking (Sentry, etc.)
define('PERFORMANCE_MONITORING', false);  // Enable performance monitoring

// ============================================================================
// BACKUP CONFIGURATION
// ============================================================================

define('BACKUP_ENABLED', true);           // Enable automatic backups
define('BACKUP_PATH', __DIR__ . '/backups'); // Backup storage path
define('BACKUP_FREQUENCY', 'daily');      // Backup frequency: hourly, daily, weekly
define('BACKUP_RETENTION_DAYS', 30);      // Number of days to retain backups

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Get configuration value with optional default fallback
 * 
 * @param string $key Configuration key name
 * @param mixed $default Default value if constant not defined
 * @return mixed Configuration value
 */
function config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Check if application is in production environment
 * 
 * @return bool
 */
function is_production() {
    return APP_ENV === 'production';
}

/**
 * Check if debug mode is enabled
 * 
 * @return bool
 */
function is_debug() {
    return APP_DEBUG && !is_production();
}

/**
 * Get upload directory path
 * 
 * @param string $subdirectory Optional subdirectory
 * @return string
 */
function get_upload_dir($subdirectory = '') {
    $dir = UPLOAD_DIR;
    if ($subdirectory) {
        $dir .= '/' . trim($subdirectory, '/');
    }
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

/**
 * Get log directory path
 * 
 * @return string
 */
function get_log_dir() {
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    return LOG_PATH;
}

// ============================================================================
// INITIALIZE ENVIRONMENT
// ============================================================================

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Set error reporting based on environment
if (is_debug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Verify required directories exist
$required_dirs = [
    UPLOAD_DIR,
    LOG_PATH,
    SESSION_PATH,
    CACHE_PATH,
    BACKUP_PATH
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

?>
