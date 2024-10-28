<?php
    session_start();

    function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    if (!isAjaxRequest()) {
        echo "Unrestricted access is not allowed.";
        exit;
    }

    if (isset($_SESSION['account'])) {
        if (!$_SESSION['account']['is_staff']) {
            header('location: ../account/login.php');
            exit;
        }
    } else {
        header('location: ../account/login.php');
        exit;
    }

    require_once '../includes/head.php';
?>