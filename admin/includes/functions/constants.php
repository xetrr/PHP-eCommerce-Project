<?php
/**
 * Application Constants
 * 
 * Constants make your code more readable and maintainable.
 * Instead of magic numbers scattered throughout your code,
 * use meaningful constant names.
 * 
 * Usage: Include this file in init.php
 * include $func . 'constants.php';
 * 
 * Then use: if ($user['GroupID'] == USER_GROUP_ADMIN) { ... }
 */

// User Groups
// These represent different user roles in your system
define('USER_GROUP_ADMIN', 1);      // Administrator
define('USER_GROUP_MEMBER', 0);    // Regular member

// Registration Status
// Used to track if a user account is approved or pending
define('REG_STATUS_PENDING', 0);   // User registered but not approved
define('REG_STATUS_APPROVED', 1);  // User account is active

// Item Status
// Represents the condition/status of items/products
define('ITEM_STATUS_NEW', 1);          // Brand new item
define('ITEM_STATUS_LIKE_NEW', 2);     // Used but looks new
define('ITEM_STATUS_USED', 3);         // Used item

// Category Visibility
// Controls whether category is visible to users
define('CATEGORY_VISIBLE', 0);     // Category is visible
define('CATEGORY_HIDDEN', 1);      // Category is hidden

// Category Settings
// Controls category features
define('COMMENTS_ENABLED', 0);     // Comments allowed
define('COMMENTS_DISABLED', 1);    // Comments not allowed
define('ADS_ENABLED', 0);          // Ads allowed
define('ADS_DISABLED', 1);         // Ads not allowed

// Pagination
// Default number of items per page
define('ITEMS_PER_PAGE', 20);      // Show 20 items per page

// File Upload
// Maximum file size for uploads (in bytes)
// 5MB = 5 * 1024 * 1024
define('MAX_UPLOAD_SIZE', 5242880); // 5MB

// Allowed image types for uploads
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Session timeout (in seconds)
// 30 minutes = 30 * 60
define('SESSION_TIMEOUT', 1800);

/**
 * Get item status name
 * 
 * Helper function to convert status code to readable name
 * 
 * @param int $status Status code
 * @return string Status name
 */
function getItemStatusName($status) {
    switch ($status) {
        case ITEM_STATUS_NEW:
            return 'New';
        case ITEM_STATUS_LIKE_NEW:
            return 'Like New';
        case ITEM_STATUS_USED:
            return 'Used';
        default:
            return 'Unknown';
    }
}

/**
 * Get registration status name
 * 
 * @param int $status Status code
 * @return string Status name
 */
function getRegStatusName($status) {
    return $status == REG_STATUS_APPROVED ? 'Approved' : 'Pending';
}



