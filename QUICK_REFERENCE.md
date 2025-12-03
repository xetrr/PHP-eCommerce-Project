# Quick Reference Guide

## 🚀 Quick Start Checklist

### Immediate Actions (Do These First!)

1. ✅ **Add Security Functions**
   - File created: `admin/includes/functions/security.php`
   - Update `init.php` to include it

2. ✅ **Fix Password Hashing**
   - Replace `sha1()` with `hashPassword()`
   - Update login to use `verifyPassword()`

3. ✅ **Add XSS Protection**
   - Wrap all output with `escape()`
   - Example: `echo escape($item['name']);`

4. ✅ **Complete Missing Features**
   - Implement Edit/Update/Delete for items
   - Fix dashboard statistics

---

## 📝 Common Code Patterns

### Displaying Data Safely

**❌ Bad (Vulnerable to XSS):**
```php
echo $item['name'];
echo '<td>' . $row['Username'] . '</td>';
```

**✅ Good (Protected):**
```php
echo escape($item['name']);
echo '<td>' . escape($row['Username']) . '</td>';
```

### Password Handling

**❌ Bad (Insecure):**
```php
$pass = sha1($_POST['password']);
// Login check
if ($hashedPass == $storedHash) { ... }
```

**✅ Good (Secure):**
```php
// When creating password
$pass = hashPassword($_POST['password']);

// When verifying password
if (verifyPassword($inputPassword, $storedHash)) { ... }
```

### Form with CSRF Protection

**Form:**
```php
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <!-- other fields -->
</form>
```

**Processing:**
```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        die('Invalid request');
    }
    // Process form...
}
```

### Input Validation

```php
$formErrors = [];

// Required field
if (empty(trim($_POST['name']))) {
    $formErrors[] = "Name is required";
}

// Email validation
if (!validateEmail($_POST['email'])) {
    $formErrors[] = "Invalid email format";
}

// Numeric validation
if (!validateNumeric($_POST['price'])) {
    $formErrors[] = "Price must be a number";
}

// Display errors
if (!empty($formErrors)) {
    foreach ($formErrors as $error) {
        echo '<div class="alert alert-danger">' . escape($error) . '</div>';
    }
}
```

### Database Queries (You're Already Doing This Right!)

**✅ Good (Using Prepared Statements):**
```php
$stmt = $con->prepare("SELECT * FROM users WHERE Username = ? AND Password = ?");
$stmt->execute([$username, $hashedPass]);
```

**❌ Bad (SQL Injection Risk):**
```php
$query = "SELECT * FROM users WHERE Username = '$username'";
$result = $con->query($query);
```

---

## 🔧 Function Reference

### Security Functions

| Function | Purpose | Example |
|----------|---------|---------|
| `escape($string)` | Prevent XSS attacks | `echo escape($userInput);` |
| `hashPassword($pass)` | Hash password securely | `$hash = hashPassword('mypass');` |
| `verifyPassword($pass, $hash)` | Verify password | `if (verifyPassword($input, $stored))` |
| `generateCSRFToken()` | Generate CSRF token | `<input name="csrf_token" value="<?php echo generateCSRFToken(); ?>">` |
| `verifyCSRFToken($token)` | Verify CSRF token | `if (!verifyCSRFToken($_POST['csrf_token']))` |
| `sanitizeInput($input)` | Clean user input | `$clean = sanitizeInput($_POST['name']);` |
| `validateEmail($email)` | Check email format | `if (validateEmail($email))` |

### Existing Functions (from functions.php)

| Function | Purpose | Example |
|----------|---------|---------|
| `checkItem($col, $table, $value)` | Check if value exists | `if (checkItem('Username', 'users', 'john') > 0)` |
| `countItems($col, $table)` | Count records | `echo countItems('user_id', 'users');` |
| `getLatest($select, $table, $order, $limit)` | Get latest records | `$users = getLatest('*', 'users', 'user_id', 5);` |
| `redirectHome($msg, $url, $seconds)` | Redirect with message | `redirectHome('Success!', 'dashboard.php', 2);` |

### Constants (from constants.php)

| Constant | Value | Usage |
|----------|-------|-------|
| `USER_GROUP_ADMIN` | 1 | Admin user group |
| `USER_GROUP_MEMBER` | 0 | Regular member |
| `REG_STATUS_PENDING` | 0 | Pending approval |
| `REG_STATUS_APPROVED` | 1 | Approved account |
| `ITEM_STATUS_NEW` | 1 | New item |
| `ITEM_STATUS_LIKE_NEW` | 2 | Like new item |
| `ITEM_STATUS_USED` | 3 | Used item |

---

## 🎯 File Structure

```
admin/
├── includes/
│   ├── functions/
│   │   ├── config.php          (Database config)
│   │   ├── functions.php        (Helper functions)
│   │   ├── security.php         (NEW - Security functions)
│   │   └── constants.php         (NEW - Constants)
│   ├── templates/
│   │   ├── header.php
│   │   ├── navbar.php
│   │   └── footer.php
│   └── languages/
│       ├── english.php
│       └── arablic.php
├── items.php                    (Item management)
├── members.php                  (User management)
├── categories.php               (Category management)
├── dashboard.php                (Admin dashboard)
├── index.php                    (Login page)
├── init.php                     (Initialize - include this in all pages)
└── connect.php                  (Database connection)
```

---

## 🔍 Common Issues & Solutions

### Issue: "Call to undefined function escape()"
**Solution:** Make sure you included `security.php` in `init.php`

### Issue: "Password verification not working"
**Solution:** 
- Old passwords were hashed with SHA1, new ones use password_hash()
- You need to reset passwords or migrate existing ones
- For new users, use `hashPassword()` when creating

### Issue: "CSRF token mismatch"
**Solution:**
- Make sure session is started before generating token
- Token must be in form AND verified in processing
- Don't regenerate token on every page load (only if it doesn't exist)

### Issue: "SQL error in checkItem()"
**Solution:**
- Make sure column and table names are in the whitelist
- Check the allowed arrays in the improved `checkItem()` function

---

## 📚 Learning Path

### Beginner Level (You Are Here)
- ✅ Understanding PHP basics
- ✅ Using PDO and prepared statements
- ✅ Session management
- ✅ Form handling

### Intermediate Level (Next Steps)
- [ ] Object-oriented PHP
- [ ] MVC pattern
- [ ] Error handling and logging
- [ ] Input validation classes
- [ ] Database abstraction layer

### Advanced Level (Future)
- [ ] Design patterns
- [ ] API development
- [ ] Testing (PHPUnit)
- [ ] Performance optimization
- [ ] Caching strategies

---

## 💡 Pro Tips

1. **Always escape output** - Even if you trust the data source
2. **Use prepared statements** - Never concatenate user input into SQL
3. **Validate on both client and server** - JavaScript for UX, PHP for security
4. **Keep functions small** - One function, one purpose
5. **Comment complex logic** - Future you will thank present you
6. **Test with malicious input** - Try `<script>`, SQL injection attempts, etc.
7. **Use version control** - You're using Git, great!
8. **Read error messages** - They tell you what's wrong
9. **Don't trust user input** - Validate everything
10. **Keep learning** - Programming is a journey, not a destination

---

## 🆘 Getting Help

### When Stuck:
1. Read the error message carefully
2. Check PHP documentation: https://www.php.net/manual/en/
3. Search Stack Overflow
4. Check your code against examples in this guide
5. Use `var_dump()` or `print_r()` to debug

### Useful PHP Functions to Know:
- `var_dump()` - Debug variable contents
- `print_r()` - Print readable array/object
- `isset()` - Check if variable exists
- `empty()` - Check if variable is empty
- `trim()` - Remove whitespace
- `strlen()` - Get string length
- `is_numeric()` - Check if value is number
- `filter_var()` - Validate/sanitize data

---

## ✅ Code Review Checklist

Before submitting code, check:

- [ ] All user input is validated
- [ ] All output is escaped with `escape()`
- [ ] Passwords use `hashPassword()` / `verifyPassword()`
- [ ] Forms have CSRF protection
- [ ] Database queries use prepared statements
- [ ] Error messages are user-friendly
- [ ] Code is commented where needed
- [ ] No hardcoded values (use constants)
- [ ] No SQL injection risks
- [ ] No XSS vulnerabilities

---

Good luck with your project! 🚀



