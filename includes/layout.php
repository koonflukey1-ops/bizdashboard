<?php
declare(strict_types=1);
require_once __DIR__.'/functions.php';

function icon(string $name): string {
    $icons=['home'=>'<path d="M3 11l9-8 9 8v9a1 1 0 01-1 1h-5v-7H9v7H4a1 1 0 01-1-1z"/>','book'=>'<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5z"/><path d="M4 5.5v14"/>','calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>','users'=>'<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>','return'=>'<path d="M9 14l-4-4 4-4M5 10h10a4 4 0 014 4v6"/>','history'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>','wallet'=>'<rect x="2" y="5" width="20" height="15" rx="2"/><path d="M16 13h4M2 9h20"/>','logout'=>'<path d="M10 17l5-5-5-5M15 12H3M21 19V5a2 2 0 00-2-2h-6"/>','store'=>'<path d="M3 9l2-6h14l2 6M5 13v8h14v-8M9 21v-6h6v6"/>'];
    return '<svg viewBox="0 0 24 24" aria-hidden="true">'.($icons[$name]??$icons['book']).'</svg>';
}
function page_start(string $title,string $active=''): void {
    $u=current_user(); $admin=($u['role']??'')==='admin'; $toast=$_SESSION['toast']??null; unset($_SESSION['toast']);
    echo '<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' · Paper & Page</title><link rel="stylesheet" href="'.base_url('assets/css/style.css').'"></head><body>';
    if(!$u){echo '<main class="auth-shell">';return;}
    $adminLinks=[['dashboard','home','ภาพรวม','admin/dashboard.php'],['books','book','จัดการหนังสือ','admin/books.php'],['reservations','calendar','รายการจอง','admin/reservations.php'],['walkin','store','เช่าหน้าร้าน','admin/walkin-rental.php'],['returns','return','รับคืนหนังสือ','admin/returns.php'],['history','history','ประวัติการเช่า','admin/rental-history.php'],['members','users','สมาชิก','admin/members.php'],['revenue','wallet','รายได้','admin/revenue.php']];
    $memberLinks=[['store','book','เลือกหนังสือ','member/books.php'],['mine','history','การจอง / การเช่าของฉัน','member/my-rentals.php']];
    echo '<div class="app"><aside class="sidebar"><a class="logo" href="'.base_url($admin?'admin/dashboard.php':'member/books.php').'"><span>'.icon('book').'</span><b>Paper & Page<small>BOOK RENTAL</small></b></a><nav>';
    foreach($admin?$adminLinks:$memberLinks as $l) echo '<a class="'.($active===$l[0]?'active':'').'" href="'.base_url($l[3]).'">'.icon($l[1]).'<span>'.e($l[2]).'</span></a>';
    echo '</nav><div class="sidebar-user"><small>เข้าสู่ระบบโดย</small><b>'.e($u['fullname']).'</b><span>'.($admin?'ผู้ดูแลระบบ':'สมาชิก').'</span><a href="'.base_url('logout.php').'">'.icon('logout').' ออกจากระบบ</a></div></aside><main class="main"><header class="topbar"><button class="menu-btn" data-menu>'.icon('book').'</button><div><b>'.e($title).'</b><small>'.date('d F Y').'</small></div><span class="avatar">'.e(mb_substr($u['fullname'],0,1)).'</span></header><div class="content">';
    if($toast) echo '<div class="toast '.$toast['type'].'" data-toast>'.e($toast['message']).'</div>';
}
function page_end(): void { echo current_user()?'</div></main></div>':'</main>'; echo '<script src="'.base_url('assets/js/app.js').'" defer></script></body></html>'; }
function status_badge(string $status,string $label=''): string { return '<span class="badge '.e($status).'">'.e($label?:$status).'</span>'; }
