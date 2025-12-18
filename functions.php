<?php
/**
 * Reusable Helper Functions for Database Entry System
 * 
 * This file contains common utility functions to reduce code duplication
 * across the application. All functions are designed to be DRY-compliant.
 * 
 * Created: 2025-12-18
 */

// ============================================================================
// DATABASE FUNCTIONS
// ============================================================================

/**
 * Establish database connection
 * 
 * @param string $host Database host
 * @param string $user Database user
 * @param string $password Database password
 * @param string $database Database name
 * 
 * @return mysqli|false Database connection object or false on failure
 */
function connect_database($host, $user, $password, $database) {
    $conn = new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        log_error("Database connection failed: " . $conn->connect_error);
        return false;
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

/**
 * Close database connection
 * 
 * @param mysqli $conn Database connection object
 * 
 * @return bool True if closed successfully
 */
function close_database($conn) {
    if ($conn && $conn instanceof mysqli) {
        return $conn->close();
    }
    return false;
}

/**
 * Execute a prepared statement safely
 * 
 * @param mysqli $conn Database connection
 * @param string $query SQL query with placeholders
 * @param array $params Parameters to bind to query
 * @param string $types Data types of parameters (e.g., "ssi" for string, string, int)
 * 
 * @return mysqli_result|bool Query result or false on failure
 */
function execute_query($conn, $query, $params = [], $types = "") {
    if (!$conn || !($conn instanceof mysqli)) {
        log_error("Invalid database connection");
        return false;
    }
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        log_error("Query preparation failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params) && !empty($types)) {
        if (!$stmt->bind_param($types, ...$params)) {
            log_error("Parameter binding failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }
    
    if (!$stmt->execute()) {
        log_error("Query execution failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result;
}

/**
 * Fetch all rows from query result
 * 
 * @param mysqli_result $result Query result object
 * 
 * @return array Array of associative arrays representing rows
 */
function fetch_all_rows($result) {
    if (!$result || !($result instanceof mysqli_result)) {
        return [];
    }
    
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    return $rows;
}

/**
 * Fetch a single row from query result
 * 
 * @param mysqli_result $result Query result object
 * 
 * @return array|null Associative array representing the row or null
 */
function fetch_single_row($result) {
    if (!$result || !($result instanceof mysqli_result)) {
        return null;
    }
    
    return $result->fetch_assoc();
}

/**
 * Get the last inserted ID
 * 
 * @param mysqli $conn Database connection
 * 
 * @return int Last inserted ID
 */
function get_last_insert_id($conn) {
    if (!$conn || !($conn instanceof mysqli)) {
        return 0;
    }
    
    return $conn->insert_id;
}

// ============================================================================
// VALIDATION FUNCTIONS
// ============================================================================

/**
 * Validate email address
 * 
 * @param string $email Email address to validate
 * 
 * @return bool True if valid email, false otherwise
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate required field is not empty
 * 
 * @param string $value Value to check
 * 
 * @return bool True if not empty, false otherwise
 */
function is_required_field($value) {
    return !empty(trim($value));
}

/**
 * Validate string length is within range
 * 
 * @param string $value String to validate
 * @param int $min Minimum length
 * @param int $max Maximum length
 * 
 * @return bool True if within range, false otherwise
 */
function is_valid_length($value, $min, $max) {
    $length = strlen(trim($value));
    return $length >= $min && $length <= $max;
}

/**
 * Validate numeric value
 * 
 * @param mixed $value Value to check
 * @param bool $allow_decimal Allow decimal numbers
 * 
 * @return bool True if numeric, false otherwise
 */
function is_numeric_value($value, $allow_decimal = false) {
    if ($allow_decimal) {
        return is_numeric($value);
    }
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

/**
 * Validate date format
 * 
 * @param string $date Date string to validate
 * @param string $format Expected date format (default: Y-m-d)
 * 
 * @return bool True if valid date, false otherwise
 */
function is_valid_date($date, $format = "Y-m-d") {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate phone number (basic validation)
 * 
 * @param string $phone Phone number to validate
 * 
 * @return bool True if valid phone format, false otherwise
 */
function is_valid_phone($phone) {
    // Remove common separators
    $phone = preg_replace("/[^0-9]/", "", $phone);
    
    // Check if between 7 and 15 digits (international standard)
    return strlen($phone) >= 7 && strlen($phone) <= 15;
}

// ============================================================================
// SANITIZATION FUNCTIONS
// ============================================================================

/**
 * Sanitize string input
 * 
 * @param string $input Input string to sanitize
 * 
 * @return string Sanitized string
 */
function sanitize_string($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize email input
 * 
 * @param string $email Email to sanitize
 * 
 * @return string Sanitized email
 */
function sanitize_email($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitize numeric input
 * 
 * @param mixed $input Numeric value to sanitize
 * @param bool $allow_decimal Allow decimal numbers
 * 
 * @return int|float Sanitized numeric value
 */
function sanitize_number($input, $allow_decimal = false) {
    if ($allow_decimal) {
        return floatval($input);
    }
    return intval($input);
}

/**
 * Sanitize and escape SQL strings
 * 
 * @param mysqli $conn Database connection
 * @param string $input String to escape
 * 
 * @return string Escaped string
 */
function sanitize_sql($conn, $input) {
    if (!$conn || !($conn instanceof mysqli)) {
        return addslashes($input);
    }
    return $conn->real_escape_string($input);
}

// ============================================================================
// FORMATTING FUNCTIONS
// ============================================================================

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Output format (default: d/m/Y)
 * 
 * @return string Formatted date or empty string if invalid
 */
function format_date($date, $format = "d/m/Y") {
    $d = DateTime::createFromFormat("Y-m-d", $date);
    return $d ? $d->format($format) : "";
}

/**
 * Format datetime for display
 * 
 * @param string $datetime DateTime string
 * @param string $format Output format (default: d/m/Y H:i:s)
 * 
 * @return string Formatted datetime or empty string if invalid
 */
function format_datetime($datetime, $format = "d/m/Y H:i:s") {
    $d = DateTime::createFromFormat("Y-m-d H:i:s", $datetime);
    return $d ? $d->format($format) : "";
}

/**
 * Format currency for display
 * 
 * @param float $amount Amount to format
 * @param string $currency Currency symbol (default: $)
 * @param int $decimals Number of decimal places (default: 2)
 * 
 * @return string Formatted currency
 */
function format_currency($amount, $currency = "$", $decimals = 2) {
    return $currency . number_format(floatval($amount), $decimals, ".", ",");
}

/**
 * Format phone number for display
 * 
 * @param string $phone Phone number
 * @param string $format Format pattern (e.g., "XXX-XXX-XXXX")
 * 
 * @return string Formatted phone number
 */
function format_phone($phone, $format = "XXX-XXX-XXXX") {
    $phone = preg_replace("/[^0-9]/", "", $phone);
    
    $pattern = str_split($format);
    $result = "";
    $phone_index = 0;
    
    foreach ($pattern as $char) {
        if ($char === "X" && isset($phone[$phone_index])) {
            $result .= $phone[$phone_index++];
        } else if ($char !== "X") {
            $result .= $char;
        }
    }
    
    return $result;
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Generate a unique ID
 * 
 * @param string $prefix Prefix for the ID
 * 
 * @return string Unique ID
 */
function generate_unique_id($prefix = "") {
    return $prefix . uniqid(bin2hex(random_bytes(4)), true);
}

/**
 * Get current timestamp
 * 
 * @return string Current timestamp in Y-m-d H:i:s format
 */
function get_current_timestamp() {
    return date("Y-m-d H:i:s");
}

/**
 * Get current date
 * 
 * @return string Current date in Y-m-d format
 */
function get_current_date() {
    return date("Y-m-d");
}

/**
 * Check if array key exists and is not empty
 * 
 * @param array $array Array to check
 * @param string $key Key to look for
 * 
 * @return bool True if key exists and is not empty
 */
function array_key_not_empty($array, $key) {
    return isset($array[$key]) && !empty($array[$key]);
}

/**
 * Get array value with default fallback
 * 
 * @param array $array Array to access
 * @param string $key Key to retrieve
 * @param mixed $default Default value if key doesn't exist
 * 
 * @return mixed Value from array or default value
 */
function array_get($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Convert array to key-value pairs (useful for form selects)
 * 
 * @param array $data Array of data
 * @param string $value_key Key for value field
 * @param string $label_key Key for label field
 * 
 * @return array Array of key-value pairs
 */
function array_to_options($data, $value_key, $label_key) {
    $options = [];
    
    foreach ($data as $item) {
        if (isset($item[$value_key]) && isset($item[$label_key])) {
            $options[$item[$value_key]] = $item[$label_key];
        }
    }
    
    return $options;
}

/**
 * Log error messages
 * 
 * @param string $message Error message
 * @param string $log_file Path to log file (default: error.log)
 * 
 * @return bool True if logged successfully
 */
function log_error($message, $log_file = "error.log") {
    $timestamp = get_current_timestamp();
    $log_message = "[{$timestamp}] {$message}" . PHP_EOL;
    
    return error_log($log_message, 3, $log_file) !== false;
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 * @param int $status_code HTTP status code (default: 302)
 * 
 * @return void
 */
function redirect($url, $status_code = 302) {
    header("Location: {$url}", true, $status_code);
    exit;
}

/**
 * Check if request is POST
 * 
 * @return bool True if POST request
 */
function is_post_request() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 * 
 * @return bool True if GET request
 */
function is_get_request() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get POST parameter with optional default
 * 
 * @param string $key Parameter key
 * @param mixed $default Default value if not set
 * 
 * @return mixed Parameter value or default
 */
function get_post($key, $default = null) {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Get GET parameter with optional default
 * 
 * @param string $key Parameter key
 * @param mixed $default Default value if not set
 * 
 * @return mixed Parameter value or default
 */
function get_query($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * Check if session is started
 * 
 * @return bool True if session is active
 */
function is_session_active() {
    return session_status() === PHP_SESSION_ACTIVE;
}

/**
 * Start session if not already active
 * 
 * @return bool True if session started successfully
 */
function start_session() {
    if (!is_session_active()) {
        return session_start();
    }
    return true;
}

/**
 * Set flash message in session
 * 
 * @param string $key Message key
 * @param string $message Message content
 * @param string $type Message type (success, error, warning, info)
 * 
 * @return bool True if set successfully
 */
function set_flash_message($key, $message, $type = "info") {
    start_session();
    $_SESSION['flash'][$key] = [
        'message' => $message,
        'type' => $type
    ];
    return true;
}

/**
 * Get flash message from session
 * 
 * @param string $key Message key
 * @param bool $delete Delete after retrieving
 * 
 * @return array|null Flash message array with 'message' and 'type' or null
 */
function get_flash_message($key, $delete = true) {
    start_session();
    
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }
    
    $message = $_SESSION['flash'][$key];
    
    if ($delete) {
        unset($_SESSION['flash'][$key]);
    }
    
    return $message;
}

// ============================================================================
// SECURITY FUNCTIONS
// ============================================================================

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generate_csrf_token() {
    start_session();
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * 
 * @return bool True if token is valid
 */
function verify_csrf_token($token) {
    start_session();
    
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password using bcrypt
 * 
 * @param string $password Password to hash
 * @param int $cost Cost parameter (default: 10)
 * 
 * @return string Hashed password
 */
function hash_password($password, $cost = 10) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
}

/**
 * Verify password against hash
 * 
 * @param string $password Plain password to verify
 * @param string $hash Hash to check against
 * 
 * @return bool True if password matches hash
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if password needs rehashing (for upgraded algorithms)
 * 
 * @param string $hash Password hash to check
 * @param int $cost Cost parameter (default: 10)
 * 
 * @return bool True if needs rehashing
 */
function needs_rehash($hash, $cost = 10) {
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $cost]);
}

// ============================================================================
// PAGINATION FUNCTIONS
// ============================================================================

/**
 * Get pagination data
 * 
 * @param int $total_rows Total number of rows
 * @param int $rows_per_page Rows to display per page
 * @param int $current_page Current page number
 * 
 * @return array Pagination data (offset, limit, total_pages, current_page)
 */
function get_pagination($total_rows, $rows_per_page, $current_page = 1) {
    $current_page = max(1, intval($current_page));
    $total_pages = ceil($total_rows / $rows_per_page);
    $current_page = min($current_page, $total_pages);
    
    $offset = ($current_page - 1) * $rows_per_page;
    
    return [
        'offset' => $offset,
        'limit' => $rows_per_page,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'total_rows' => $total_rows
    ];
}

// ============================================================================
// JSON RESPONSE FUNCTIONS
// ============================================================================

/**
 * Send JSON response
 * 
 * @param bool $success Success status
 * @param string $message Response message
 * @param mixed $data Response data
 * @param int $status_code HTTP status code
 * 
 * @return void
 */
function send_json_response($success, $message = "", $data = null, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];
    
    echo json_encode($response);
    exit;
}

/**
 * Send JSON success response
 * 
 * @param string $message Success message
 * @param mixed $data Response data
 * 
 * @return void
 */
function json_success($message = "Operation successful", $data = null) {
    send_json_response(true, $message, $data, 200);
}

/**
 * Send JSON error response
 * 
 * @param string $message Error message
 * @param mixed $data Response data
 * @param int $status_code HTTP status code (default: 400)
 * 
 * @return void
 */
function json_error($message = "An error occurred", $data = null, $status_code = 400) {
    send_json_response(false, $message, $data, $status_code);
}

?>
