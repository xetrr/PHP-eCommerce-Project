# Step-by-Step Implementation Guide

This guide will help you implement the improvements one step at a time.

## 🚀 Quick Start: Critical Security Fixes

### Step 1: Create Security Helper Functions

Create a new file: `admin/includes/functions/security.php`

```php
<?php
/**
 * Security Helper Functions
 * 
 * These functions help protect your application from common attacks
 */

/**
 * Escape output to prevent XSS attacks
 * 
 * @param string $string The string to escape
 * @return string Escaped string safe for HTML output
 * 
 * Example:
 * echo escape($userInput); // Safe to display in HTML
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token for form protection
 * 
 * @return string CSRF token
 * 
 * Usage in form:
 * <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 * 
 * Usage after form submission:
 * if (!verifyCSRFToken($_POST['csrf_token'])) {
 *     die('Invalid request');
 * }
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password securely
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 * 
 * Example:
 * $hashed = hashPassword('mypassword123');
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 * 
 * @param string $password Plain text password
 * @param string $hash Stored password hash
 * @return bool True if password matches
 * 
 * Example:
 * if (verifyPassword($inputPassword, $storedHash)) {
 *     // Login successful
 * }
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize input string
 * 
 * @param string $input User input
 * @return string Sanitized string
 */
function sanitizeInput($input) {
    return filter_var(trim($input), FILTER_SANITIZE_STRING);
}

/**
 * Validate email
 * 
 * @param string $email Email address
 * @return bool True if valid email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
```

**How to use:**
1. Add this file to your project
2. Include it in `init.php`: `include $func . 'security.php';`
3. Replace all direct output with `escape()`
4. Use `hashPassword()` instead of `sha1()`

---

### Step 2: Update init.php to Include Security Functions

Edit `admin/init.php`:

```php
<?php
include 'connect.php';
//Routes
$tpl = '../admin/includes/templates/';
$lang = 'includes/languages/';
$func = 'includes/functions/';

include $func . 'functions.php';
include $func . 'security.php'; // ← Add this line
include $lang . 'english.php';
include $tpl . 'header.php';

if (!isset($noNavbar)) {
    include $tpl . 'navbar.php';
}
include $tpl . 'footer.php';
```

---

### Step 3: Fix Password Hashing in members.php

**Before (Line 108):**
```php
$pass = sha1($_POST['password']);
```

**After:**
```php
$pass = hashPassword($_POST['password']);
```

**Also update login (index.php line 13):**
```php
// Before:
$hashedPass = sha1($password);

// After:
// Don't hash here! Get the hash from database and verify
$stmt = $con->prepare("SELECT user_id, Username, Password FROM users WHERE Username = ? AND groupID = ? LIMIT 1");
$stmt->execute([$username, 1]);
$row = $stmt->fetch();

if ($row && verifyPassword($password, $row['Password'])) {
    $_SESSION['Username'] = $username;
    $_SESSION['ID'] = $row['user_id'];
    header('location: dashboard.php');
    exit();
}
```

---

### Step 4: Add XSS Protection

**Before:**
```php
echo '<td>' . $item['name'] . '</td>';
```

**After:**
```php
echo '<td>' . escape($item['name']) . '</td>';
```

**Apply this everywhere you output user data:**
- `items.php` lines 33-38
- `members.php` lines 35-39
- `categories.php` line 42
- Any other place displaying data from database

---

### Step 5: Improve checkItem() Function

Edit `admin/includes/functions/functions.php`:

**Before:**
```php
function checkItem($select, $from, $value) {
    global $con;
    $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
    $statement->execute([$value]);
    $count = $statement->rowCount();
    return $count;
}
```

**After:**
```php
function checkItem($select, $from, $value) {
    global $con;
    
    // Whitelist allowed columns and tables to prevent SQL injection
    $allowedColumns = [
        'user_id', 'Username', 'Email', 'FullName', 'Password', 'RegStatus',
        'item_id', 'name', 'description', 'price',
        'catid', 'ordering'
    ];
    $allowedTables = ['users', 'items', 'categories'];
    
    // Validate inputs
    if (!in_array($select, $allowedColumns)) {
        error_log("Invalid column: $select");
        return 0;
    }
    if (!in_array($from, $allowedTables)) {
        error_log("Invalid table: $from");
        return 0;
    }
    
    try {
        $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
        $statement->execute([$value]);
        return $statement->rowCount();
    } catch (PDOException $e) {
        error_log("Database error in checkItem: " . $e->getMessage());
        return 0;
    }
}
```

---

## 📝 Complete Missing Features

### Step 6: Implement Item Edit Functionality

Add this to `items.php` after line 198:

```php
} elseif ($do == 'Edit') {
    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
    
    $stmt = $con->prepare('SELECT * FROM items WHERE item_id = ? LIMIT 1');
    $stmt->execute([$itemid]);
    $item = $stmt->fetch();
    
    if ($stmt->rowCount() > 0) { ?>
        <h1 class="text-center">Edit Item</h1>
        <div class="container">
            <form action="?do=update" method="POST" class="form-horizontal">
                <input type="hidden" name="itemid" value="<?php echo $itemid; ?>">
                
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label class="col-sm-1 col-form-label">Item Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo escape($item['name']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label class="col-sm-1 col-form-label">Description</label>
                    <div class="col-sm-10 col-md-6">
                        <textarea name="desc" class="form-control" required><?php echo escape($item['description']); ?></textarea>
                    </div>
                </div>
                
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label class="col-sm-1 col-form-label">Price</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="number" step="0.01" name="price" class="form-control" 
                               value="<?php echo escape($item['price']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label class="col-sm-1 col-form-label">Made In</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="country" class="form-control" 
                               value="<?php echo escape($item['country_made']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label class="col-sm-1 col-form-label">Status</label>
                    <div class="col-sm-10 col-md-6">
                        <select class="form-control" name="status" required>
                            <option value="0">...</option>
                            <option value="1" <?php echo $item['status'] == 1 ? 'selected' : ''; ?>>New</option>
                            <option value="2" <?php echo $item['status'] == 2 ? 'selected' : ''; ?>>Like New</option>
                            <option value="3" <?php echo $item['status'] == 3 ? 'selected' : ''; ?>>Used</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label class="col-sm-1 col-form-label">Member</label>
                    <div class="col-sm-10 col-md-6">
                        <select class="form-control" name="member" required>
                            <option value="0">...</option>
                            <?php
                            $stmt = $con->prepare("SELECT * FROM users");
                            $stmt->execute();
                            $users = $stmt->fetchAll();
                            foreach ($users as $user) {
                                $selected = $item['member_id'] == $user['user_id'] ? 'selected' : '';
                                echo "<option value='" . $user['user_id'] . "' $selected>" . escape($user['Username']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group form-group-lg row g-1 align-items-center mb-2">
                    <label class="col-sm-1 col-form-label">Category</label>
                    <div class="col-sm-10 col-md-6">
                        <select class="form-control" name="category" required>
                            <option value="0">...</option>
                            <?php
                            $stmt2 = $con->prepare("SELECT * FROM categories");
                            $stmt2->execute();
                            $cats = $stmt2->fetchAll();
                            foreach ($cats as $cat) {
                                $selected = $item['cat_id'] == $cat['catid'] ? 'selected' : '';
                                echo "<option value='" . $cat['catid'] . "' $selected>" . escape($cat['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10 col-md-6">
                        <input type="submit" value="Update Item" class="btn btn-primary btn-lg">
                    </div>
                </div>
            </form>
        </div>
    <?php } else {
        echo '<div class="container"><div class="alert alert-danger">Item not found</div></div>';
    }
```

### Step 7: Implement Item Update

Add this to `items.php` after the Edit section:

```php
} elseif ($do == 'update') {
    echo "<h1 class='text-center'>Update Item</h1>";
    echo "<div class='container'>";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $itemid = isset($_POST['itemid']) && is_numeric($_POST['itemid']) ? intval($_POST['itemid']) : 0;
        $name = sanitizeInput($_POST['name']);
        $desc = sanitizeInput($_POST['desc']);
        $price = $_POST['price'];
        $country = sanitizeInput($_POST['country']);
        $status = $_POST['status'];
        $member = $_POST['member'];
        $category = $_POST['category'];
        
        $formErrors = [];
        
        if (empty($name)) {
            $formErrors[] = "Name can't be empty";
        }
        if (empty($desc)) {
            $formErrors[] = "Description can't be empty";
        }
        if (empty($price) || !is_numeric($price)) {
            $formErrors[] = "Price must be a valid number";
        }
        if (empty($country)) {
            $formErrors[] = "Country can't be empty";
        }
        if ($status == 0) {
            $formErrors[] = "Status must be selected";
        }
        if ($member == 0) {
            $formErrors[] = "Member must be selected";
        }
        if ($category == 0) {
            $formErrors[] = "Category must be selected";
        }
        
        if (empty($formErrors)) {
            try {
                $stmt = $con->prepare("UPDATE items SET 
                    name = :name, 
                    description = :desc, 
                    price = :price, 
                    country_made = :country, 
                    status = :status, 
                    member_id = :member, 
                    cat_id = :category 
                    WHERE item_id = :itemid");
                
                $stmt->execute([
                    'name' => $name,
                    'desc' => $desc,
                    'price' => $price,
                    'country' => $country,
                    'status' => $status,
                    'member' => $member,
                    'category' => $category,
                    'itemid' => $itemid
                ]);
                
                $userMsg = "<div class='alert alert-success'>Item updated successfully</div>";
                redirectHome($userMsg, 'items.php');
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error: " . escape($e->getMessage()) . "</div>";
            }
        } else {
            foreach ($formErrors as $error) {
                echo '<div class="alert alert-danger">' . escape($error) . '</div>';
            }
            echo '<a href="items.php?do=Edit&itemid=' . $itemid . '" class="btn btn-primary">Go Back</a>';
        }
    } else {
        redirectHome('Invalid request', 'items.php');
    }
```

### Step 8: Implement Item Delete

Add this to `items.php`:

```php
} elseif ($do == 'delete') {
    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
    
    $check = checkItem('item_id', 'items', $itemid);
    
    if ($check > 0) {
        try {
            $stmt = $con->prepare('DELETE FROM items WHERE item_id = ?');
            $stmt->execute([$itemid]);
            $userMsg = "<div class='alert alert-success'>Item deleted successfully</div>";
            redirectHome($userMsg, 'items.php');
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error deleting item: " . escape($e->getMessage()) . "</div>";
        }
    } else {
        redirectHome('Item not found', 'items.php');
    }
```

---

## 🔧 Fix Dashboard Statistics

### Step 9: Update dashboard.php

**Line 36 - Before:**
```php
<span>1020</span>
```

**After:**
```php
<span><a href="items.php"><?php echo countItems('item_id', 'items'); ?></a></span>
```

**Line 40-41 - Before:**
```php
<span>40</span>
```

**After:**
```php
<span><?php echo countItems('comment_id', 'comments'); ?></span>
```
*(Assuming you have a comments table - if not, remove this stat or create the table)*

---

## 📋 Testing Checklist

After implementing changes, test:

- [ ] Login with old password (should fail - need to reset passwords)
- [ ] Create new user with new password hashing
- [ ] Edit item and verify data is escaped
- [ ] Delete item and verify it's removed
- [ ] Check dashboard shows correct counts
- [ ] Try entering `<script>alert('xss')</script>` in forms - should be escaped

---

## 🎓 Understanding the Code

### Why password_hash() is Better

**SHA1 Problems:**
- Fast to compute (millions per second)
- No built-in salt
- Vulnerable to rainbow tables
- Cryptographically broken

**password_hash() Benefits:**
- Uses bcrypt/argon2 (slow by design)
- Automatically generates unique salt
- Future-proof (can upgrade algorithm)
- Built-in to PHP

### Why escape() is Important

**Without escaping:**
```php
// User enters: <script>alert('Hacked!')</script>
echo $userInput; // Executes JavaScript!
```

**With escaping:**
```php
echo escape($userInput); // Displays as text: &lt;script&gt;...
```

### Why Whitelisting is Important

**Dangerous:**
```php
$column = $_GET['column']; // User could pass: "user_id; DROP TABLE users--"
$query = "SELECT $column FROM users";
```

**Safe:**
```php
$allowed = ['user_id', 'username', 'email'];
if (!in_array($column, $allowed)) {
    die('Invalid column');
}
```

---

## 🚨 Common Mistakes to Avoid

1. **Don't trust user input** - Always validate and sanitize
2. **Don't use sha1/md5** - Use password_hash()
3. **Don't echo user data directly** - Always escape
4. **Don't use string concatenation for SQL** - Use prepared statements
5. **Don't store passwords in plain text** - Always hash
6. **Don't ignore errors** - Handle them properly

---

## 📚 Next Steps

1. Implement all security fixes first
2. Complete missing features
3. Add pagination
4. Improve error handling
5. Add search functionality
6. Refactor to classes (advanced)

Good luck! 🎉



