<?php
require_once __DIR__.'/includes/functions.php';
$u=current_user();
redirect($u ? ($u['role']==='admin'?'admin/dashboard.php':'member/books.php') : 'login.php');
