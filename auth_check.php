<?php
session_start();

/* NOT LOGGED IN */
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit();
}

/* SUPER ADMIN PAGES */
if (strpos($_SERVER['PHP_SELF'], '/super_admin/') !== false) {

    if (
        $_SESSION['role'] !== 'Admin' ||
        empty($_SESSION['is_super_admin'])
    ) {
        header("Location: ../auth/super_admin_login.php");
        exit();
    }
}

/* ADMIN (NON-SUPER) PAGES */
if (
    strpos($_SERVER['PHP_SELF'], '/admin/') !== false &&
    strpos($_SERVER['PHP_SELF'], '/super_admin/') === false
) {
    if ($_SESSION['role'] !== 'Admin') {
        header("Location: ../auth/admin_login.php");
        exit();
    }
}

/* AGENT PAGES */
if (strpos($_SERVER['PHP_SELF'], '/agent/') !== false) {
    if ($_SESSION['role'] !== 'Agent') {
        header("Location: ../auth/login.php");
        exit();
    }
}

/* USER PAGES */
if (strpos($_SERVER['PHP_SELF'], '/user/') !== false) {
    if ($_SESSION['role'] !== 'End User') {
        header("Location: ../auth/login.php");
        exit();
    }
}