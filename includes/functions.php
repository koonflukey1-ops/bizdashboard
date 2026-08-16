<?php
declare(strict_types=1);

require_once __DIR__.'/auth.php';

function base_url(string $path=''): string { $root=rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])), '/'); if (str_ends_with($root,'/admin')||str_ends_with($root,'/member')||str_ends_with($root,'/actions')) $root=dirname($root); return rtrim($root,'/').'/'.ltrim($path,'/'); }
function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): never { header('Location: '.base_url($path)); exit; }
function flash(string $message,string $type='success'): void { $_SESSION['toast']=['message'=>$message,'type'=>$type]; }
function post(string $key): string { return trim((string)($_POST[$key] ?? '')); }
function valid_days(int $days): bool { return in_array($days,[3,7,14],true); }
function thai_date(?string $date,bool $time=false): string { if(!$date)return '-'; return date($time?'d/m/Y H:i':'d/m/Y',strtotime($date)); }
function rental_fee(float $price,int $days): float { return $price*$days; }
function overdue_days(string $due,?string $returned=null): int { $end=new DateTime($returned??'now'); $deadline=(new DateTime($due))->setTime(23,59,59); return $end>$deadline?(int)$deadline->diff($end)->format('%a'):0; }
function fine_amount(string $due,?string $returned=null): float { return overdue_days($due,$returned)*10; }
function book_status_label(string $s): string { return ['available'=>'ว่าง','reserved'=>'จองแล้ว','borrowed'=>'ถูกยืม'][$s]??$s; }
function reservation_label(string $s): string { return ['pending'=>'รอตรวจสอบ','approved'=>'รอรับหนังสือ','rejected'=>'ปฏิเสธ','converted'=>'เริ่มเช่าแล้ว'][$s]??$s; }
function rental_label(string $s): string { return ['borrowed'=>'กำลังยืม','returned'=>'คืนแล้ว'][$s]??$s; }
function log_activity(string $action,string $entity,int $id): void { $u=current_user(); db()->prepare('INSERT INTO activity_logs(user_id,action,entity_type,entity_id,ip_address) VALUES(?,?,?,?,?)')->execute([$u['id']??null,$action,$entity,$id,$_SERVER['REMOTE_ADDR']??'']); }
