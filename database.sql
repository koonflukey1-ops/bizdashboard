CREATE DATABASE IF NOT EXISTS book_rental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE book_rental;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(150) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  phone VARCHAR(30) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','member') NOT NULL DEFAULT 'member',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(150) NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  isbn VARCHAR(20) NULL UNIQUE,
  publisher VARCHAR(150) NULL,
  publication_year YEAR NULL,
  rental_price_per_day DECIMAL(10,2) NOT NULL,
  description TEXT NULL,
  cover_image VARCHAR(255) NULL,
  status ENUM('available','reserved','borrowed') NOT NULL DEFAULT 'available',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_books_category FOREIGN KEY(category_id) REFERENCES categories(id),
  INDEX idx_books_status(status), INDEX idx_books_title(title), INDEX idx_books_author(author)
) ENGINE=InnoDB;

CREATE TABLE reservations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  book_id INT UNSIGNED NOT NULL,
  pickup_date DATE NOT NULL,
  due_date DATE NOT NULL,
  rental_days TINYINT UNSIGNED NOT NULL,
  status ENUM('pending','approved','rejected','converted') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  approved_at DATETIME NULL,
  converted_at DATETIME NULL,
  CONSTRAINT fk_reservations_user FOREIGN KEY(user_id) REFERENCES users(id),
  CONSTRAINT fk_reservations_book FOREIGN KEY(book_id) REFERENCES books(id),
  INDEX idx_reservation_book_status(book_id,status), INDEX idx_reservation_user(user_id), INDEX idx_reservation_pickup(pickup_date)
) ENGINE=InnoDB;

CREATE TABLE rentals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  walkin_name VARCHAR(150) NULL,
  walkin_phone VARCHAR(30) NULL,
  book_id INT UNSIGNED NOT NULL,
  reservation_id INT UNSIGNED NULL UNIQUE,
  source ENUM('online_reservation','walkin') NOT NULL,
  rental_days TINYINT UNSIGNED NOT NULL,
  rental_price_per_day_at_time DECIMAL(10,2) NOT NULL,
  rental_total DECIMAL(10,2) NOT NULL,
  borrowed_at DATETIME NOT NULL,
  due_date DATE NOT NULL,
  returned_at DATETIME NULL,
  fine DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('borrowed','returned') NOT NULL DEFAULT 'borrowed',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rentals_user FOREIGN KEY(user_id) REFERENCES users(id),
  CONSTRAINT fk_rentals_book FOREIGN KEY(book_id) REFERENCES books(id),
  CONSTRAINT fk_rentals_reservation FOREIGN KEY(reservation_id) REFERENCES reservations(id),
  INDEX idx_rentals_status_due(status,due_date), INDEX idx_rentals_user(user_id), INDEX idx_rentals_book(book_id)
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(50) NOT NULL,
  entity_id INT UNSIGNED NOT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_logs_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_logs_created(created_at)
) ENGINE=InnoDB;

INSERT IGNORE INTO categories(name) VALUES ('วรรณกรรม'),('ธุรกิจ'),('พัฒนาตนเอง'),('ประวัติศาสตร์'),('วิทยาศาสตร์'),('การ์ตูน');
