<?php
require_once 'config.php';
require_once 'db.php';

function json_header() {
    header('Content-Type: application/json; charset=utf-8');
}

function require_admin() {
    if (empty($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit;
    }
}

function format_token($n) {
    return str_pad($n, 6, '0', STR_PAD_LEFT);
}
