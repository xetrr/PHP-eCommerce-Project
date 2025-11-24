<?php
// template page

ob_start();

session_start();

$pageTitle = '';

if (isset($_SESSION['Username'])) {
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    if ($do = 'Manage') {
    } elseif ($do = 'Add') {
    } elseif ($do = 'Insert') {
    } elseif ($do = 'Edit') {
    } elseif ($do = 'Update') {
    } elseif ($do = 'Delete') {
    }
    include 'footer.php';
} else {
    header('location: index.php');
    exit();
}
ob_end_flush();
