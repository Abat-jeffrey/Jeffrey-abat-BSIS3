<?php
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

redirect(roleHome(user()['role']));
