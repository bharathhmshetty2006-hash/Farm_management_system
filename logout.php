<?php
require_once __DIR__ . '/config/db.php';
session_unset();
session_destroy();
session_start();
set_flash('info', 'You have been logged out successfully.');
redirect('login.php');
