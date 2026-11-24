<?php
require __DIR__ . '/../inc/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check(); unset($_SESSION['admin']); session_regenerate_id(true); }
redirect('login.php');
