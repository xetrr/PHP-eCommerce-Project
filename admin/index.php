<?php
session_start();
$noNavbar = '';
$pageTitle = 'Login';
if (isset($_SESSION['Username'])) {
    header('location: dashboard.php');
}
include 'init.php';
?>
<?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['user'];
    $password = $_POST['pass'];
    $hashedPass = sha1($password); // echo $hashedPass;
    // check if the user exists or not in THE DATABASE
    $stmt = $con->prepare(
        "SELECT 
                                user_id,Username, Password 
                            FROM users 
                            WHERE 
                                 Username = ? 
                            AND 
                                 Password = ? 
                            AND 
                                 groupID = ?
                            LIMIT 1",
    );
    $stmt->execute([$username, $hashedPass, 1]);
    $row = $stmt->fetch();
    $count = $stmt->rowCount();
    echo $count;
    if ($count > 0) {
        $_SESSION['Username'] = $username;
        $_SESSION['ID'] = $row['user_id'];
        header('location: dashboard.php');
        exit();
    }
} ?>


<form class="login login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <h4 class="text-center">Admin Login</h4>
    <input class="form-control FI1" type="text" name="user" placeholder="Username" autocomplete="off">
    <input class="form-control FI2" type="password" name="pass" placeholder="Password" autocomplete="off">
    <input class="btn btn-primary btn-block LFB" type="submit" value="login">
</form>

<?php include $tpl . 'footer.php'; ?>
