<?php
include 'init.php';
include $tpl . 'header.php';
include 'includes/languages/english.php'
?>

<form action="">
    <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off">
    <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="off">
    <input class="btn btn-primary btn-block" type="submit" value="login">



</form>

<?php include $tpl . 'footer.php' ?>