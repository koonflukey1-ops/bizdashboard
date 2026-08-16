<?php
declare(strict_types=1);
require_once __DIR__.'/includes/functions.php';
$message='';
try {
    $exists=db()->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
    if(!$exists){db()->prepare('INSERT INTO users(fullname,username,phone,password,role) VALUES(?,?,?,?,?)')->execute(['ผู้ดูแลระบบ','admin','-',password_hash('admin123',PASSWORD_DEFAULT),'admin']);$message='สร้างบัญชี Admin สำเร็จแล้ว';}
    else $message='มีบัญชี Admin อยู่แล้ว';
} catch(Throwable $e){$message='กรุณานำเข้า database.sql ก่อน: '.$e->getMessage();}
?><!doctype html><html lang="th"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link rel="stylesheet" href="assets/css/style.css"><body><main class="auth-shell"><section class="auth-card"><h1>ติดตั้งระบบ</h1><p><?=e($message)?></p><a class="btn btn-primary" href="login.php">ไปหน้าเข้าสู่ระบบ</a></section></main></body></html>
