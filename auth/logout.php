<?php
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    logActivity(user()['id'], 'Logged out');
}

session_destroy();
redirect('/auth/login.php');
