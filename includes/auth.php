<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__.'/../config/database.php';

function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) { header('Location: '.base_url('login.php')); exit; } }
function require_admin(): void { require_login(); if (current_user()['role'] !== 'admin') { http_response_code(403); exit('Access denied'); } }
function require_member(): void { require_login(); if (current_user()['role'] !== 'member') { header('Location: '.base_url('admin/dashboard.php')); exit; } }
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.htmlspecialchars(csrf_token()).'">'; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) { http_response_code(419); exit('Invalid CSRF token'); } }
