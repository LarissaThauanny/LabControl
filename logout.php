<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_post();
$_SESSION = [];
session_regenerate_id(true);
flash('Você saiu da conta com segurança.');
redirect('login.php');
