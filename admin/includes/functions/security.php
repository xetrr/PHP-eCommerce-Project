<?php
/**
 * Security Helper Functions
 * 
 * These functions help protect your application from common attacks:
 * - XSS (Cross-Site Scripting)
 * - CSRF (Cross-Site Request Forgery)
 * - Password security
 * - Input sanitization
 * 
 * Usage: Include this file in init.php
 * include $func . 'security.php';
 */

/**
 * Escape output to prevent XSS attacks
 * 
 * XSS (Cross-Site Scripting) happens when malicious scripts are injected into web pages.
 * This function converts special characters to HTML entities so they display as text,
 * not as code.
 * 
 * @param string $string The string to escape
 * @return string Escaped string safe for HTML output
 * 
 * Example:
 * $userInput = "<script>alert('XSS')</script>";
 * echo escape($userInput); // Outputs: &lt;script&gt;alert('XSS')&lt;/script&gt;
 * 
 * Reference: https://www.php.net/manual/en/function.htmlspecialchars.php
 */
function escape($string) {
    // ENT_QUOTES: Escapes both single and double quotes
    // UTF-8: Character encoding
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token for form protection
 * 
 * CSRF (Cross-Site Request Forgery) attacks trick users into submitting forms
 * they didn't intend to submit. CSRF tokens verify that the form submission
 * came from your website.
 * 
 * @return string CSRF token (64 character hex string)
 * 
 * Usage in form:
 * <form method="POST">
 *     <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
 *     <!-- other form fields -->
 * </form>
 * 
 * Reference: https://owasp.org/www-community/attacks/csrf
 */
function generateCSRFToken() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Generate token if it doesn't exist
    if (!isset($_SESSION['csrf_token'])) {
        // random_bytes(32) generates 32 random bytes (256 bits)
        // bin2hex converts binary to hexadecimal (64 characters)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify (usually from $_POST['csrf_token'])
 * @return bool True if valid, false otherwise
 * 
 * Usage after form submission:
 * if (!verifyCSRFToken($_POST['csrf_token'])) {
 *     die('Invalid request - possible CSRF attack');
 * }
 * // Continue with form processing...
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    // hash_equals() prevents timing attacks
    // It compares strings in constant time
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password securely using PHP's built-in password hashing
 * 
 * This function uses bcrypt or argon2 algorithm (depending on PHP version).
 * It automatically:
 * - Generates a unique salt for each password
 * - Uses a cost factor to slow down brute force attacks
 * - Is future-proof (can upgrade algorithm without breaking existing passwords)
 * 
 * @param string $password Plain text password
 * @return string Hashed password (60+ characters)
 * 
 * Example:
 * $hashed = hashPassword('mySecurePassword123');
 * // Store $hashed in database
 * 
 * Reference: https://www.php.net/manual/en/function.password-hash.php
 */
function hashPassword($password) {
    // PASSWORD_DEFAULT uses the best available algorithm
    // Currently bcrypt, but may change in future PHP versions
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against stored hash
 * 
 * @param string $password Plain text password (from user input)
 * @param string $hash Stored password hash (from database)
 * @return bool True if password matches, false otherwise
 * 
 * Example:
 * // During login
 * $storedHash = $row['Password']; // From database
 * if (verifyPassword($inputPassword, $storedHash)) {
 *     // Login successful
 *     $_SESSION['Username'] = $username;
 * } else {
 *     // Wrong password
 *     echo "Invalid credentials";
 * }
 * 
 * Reference: https://www.php.net/manual/en/function.password-verify.php
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize input string
 * 
 * Removes HTML tags and special characters from user input.
 * Use this before storing data in database (but still use prepared statements!).
 * 
 * @param string $input User input
 * @return string Sanitized string
 * 
 * Example:
 * $clean = sanitizeInput($_POST['username']);
 * // Now safe to use in database queries (with prepared statements)
 * 
 * Note: FILTER_SANITIZE_STRING is deprecated in PHP 8.1+
 * For PHP 8.1+, use: htmlspecialchars(strip_tags(trim($input)))
 */
function sanitizeInput($input) {
    if (PHP_VERSION_ID >= 80100) {
        // PHP 8.1+ - FILTER_SANITIZE_STRING is deprecated
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    } else {
        return filter_var(trim($input), FILTER_SANITIZE_STRING);
    }
}

/**
 * Validate email address
 * 
 * @param string $email Email address to validate
 * @return bool True if valid email format, false otherwise
 * 
 * Example:
 * if (validateEmail($_POST['email'])) {
 *     // Email is valid
 * } else {
 *     echo "Invalid email format";
 * }
 * 
 * Reference: https://www.php.net/manual/en/filter.filters.validate.php
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate numeric input
 * 
 * @param mixed $value Value to check
 * @param bool $allowFloat Allow decimal numbers (default: true)
 * @return bool True if numeric
 * 
 * Example:
 * if (validateNumeric($_POST['price'])) {
 *     $price = floatval($_POST['price']);
 * }
 */
function validateNumeric($value, $allowFloat = true) {
    if ($allowFloat) {
        return is_numeric($value);
    } else {
        return ctype_digit($value);
    }
}

/**
 * Generate random string (useful for tokens, IDs, etc.)
 * 
 * @param int $length Length of string (default: 32)
 * @return string Random hexadecimal string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in
 */
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['Username']);
}

/**
 * Require user to be logged in (redirect if not)
 * 
 * @param string $redirectUrl URL to redirect to if not logged in
 */
function requireLogin($redirectUrl = 'index.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectUrl");
        exit();
    }
}



