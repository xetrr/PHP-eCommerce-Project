<?php
include 'connect.php';
//Routes
$tpl = '../admin/includes/templates/';
$lang = 'includes/languages/';
$func = 'includes/functions/';

include $func . 'functions.php';
include $lang . 'english.php';
include $tpl . 'header.php';

if (!isset($noNavbar)) {
    include $tpl . 'navbar.php';
}
include $tpl . 'footer.php';
