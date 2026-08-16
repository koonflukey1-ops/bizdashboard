# Book Rental Store Management System

ระบบบริหารร้านเช่าหนังสือสำหรับโครงงานมหาวิทยาลัย พัฒนาด้วย PHP, PDO และ MySQL โดยเก็บข้อมูลถาวรทั้งหมดในฐานข้อมูล ไม่ใช้ localStorage เป็นฐานข้อมูลหลัก

## ความสัมพันธ์ของฐานข้อมูล

- `users` มีรายการ `reservations` และ `rentals` ได้หลายรายการ
- `categories` มี `books` ได้หลายเล่ม
- `books` เชื่อมกับประวัติ `reservations` และ `rentals`
- เมื่อรับหนังสือจากการจอง ระบบจะสร้าง `rentals` ที่อ้างถึง `reservation_id`
- `activity_logs` เก็บกิจกรรมสำคัญของผู้ใช้งาน

Foreign keys รักษาความถูกต้องของข้อมูล ส่วน transaction และ `SELECT ... FOR UPDATE` ป้องกันการจองหรือเช่าหนังสือเล่มเดียวกันพร้อมกัน

## ติดตั้งด้วย XAMPP

1. ดาวน์โหลดและติดตั้ง XAMPP จากเว็บไซต์ Apache Friends
2. เปิด XAMPP Control Panel แล้ว Start **Apache** และ **MySQL**
3. คัดลอกโฟลเดอร์ `book-rental` ไปไว้ที่ `C:\xampp\htdocs\book-rental`
4. เปิด `http://localhost/phpmyadmin`
5. เลือกเมนู Import แล้วนำเข้าไฟล์ `database.sql`
6. ตรวจสอบค่าฐานข้อมูลใน `config/database.php` ค่าเริ่มต้นของ XAMPP คือ user `root` และรหัสผ่านว่าง
7. เปิด `http://localhost/book-rental/setup.php` หนึ่งครั้งเพื่อสร้าง Admin ด้วย `password_hash()`
8. เปิด `http://localhost/book-rental`

บัญชีทดสอบผู้ดูแล:

- Username: `admin`
- Password: `admin123`

หลังติดตั้งสำเร็จ แนะนำให้ลบหรือเปลี่ยนชื่อ `setup.php` และเปลี่ยนรหัสผ่าน Admin ก่อนใช้งานนอกเครื่องทดสอบ

## ลำดับทดสอบระบบ

1. เข้าระบบ Admin และเพิ่มหนังสือพร้อมภาพปก
2. ออกจากระบบ สมัครสมาชิก และเข้าสู่หน้าร้าน
3. จองหนังสือ โดยเลือกวันนี้/พรุ่งนี้ และ 3/7/14 วัน
4. กลับเข้า Admin → รายการจอง → อนุมัติ → เริ่มการเช่า
5. ทดสอบเช่าหน้าร้านทั้งแบบ Member และ Walk-in Customer
6. เปิดหน้ารับคืนและคืนหนังสือ ตรวจสอบค่าปรับ
7. ตรวจสอบประวัติการเช่า รายได้ และข้อมูลสมาชิก
8. สมาชิกตรวจสอบเฉพาะรายการของตนใน “การจอง / การเช่าของฉัน”

## กฎสำคัญที่ระบบบังคับใช้

- วันรับจากการจองเลือกได้เฉพาะวันนี้หรือพรุ่งนี้
- ระยะเวลาเช่ารองรับ 3, 7 หรือ 14 วันเท่านั้น
- ค่าเช่าถูก snapshot ไว้ใน rental จึงไม่เปลี่ยนตามราคาหนังสือภายหลัง
- ค่าปรับ 10 บาทต่อวันหลังวันครบกำหนด
- การเช่าเริ่มเมื่อ Admin ยืนยันรับหนังสือ หรือเริ่มรายการหน้าร้าน
- ใช้ session, role guard, CSRF token, prepared statements และ password hashing
- ตรวจชนิด MIME และจำกัดภาพปกไม่เกิน 3 MB

## โครงสร้างสำคัญ

```text
book-rental/
├── actions/         # POST handlers และ database transactions
├── admin/           # หน้าสำหรับผู้ดูแล
├── member/          # หน้าร้านและประวัติสมาชิก
├── assets/          # CSS และ JavaScript
├── config/          # การเชื่อมต่อ MySQL
├── includes/        # auth, functions และ layout
├── uploads/books/   # ภาพปก
├── database.sql
├── setup.php
├── login.php
└── register.php
```
