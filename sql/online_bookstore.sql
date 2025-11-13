
CREATE DATABASE IF NOT EXISTS online_bookstore;
USE online_bookstore;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) DEFAULT 'Customer',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS authors;
CREATE TABLE authors (author_id INT AUTO_INCREMENT PRIMARY KEY, author_name VARCHAR(100) NOT NULL);

DROP TABLE IF EXISTS publishers;
CREATE TABLE publishers (publisher_id INT AUTO_INCREMENT PRIMARY KEY, publisher_name VARCHAR(100) NOT NULL);

DROP TABLE IF EXISTS categories;
CREATE TABLE categories (category_id INT AUTO_INCREMENT PRIMARY KEY, category_name VARCHAR(100) NOT NULL);

DROP TABLE IF EXISTS books;
CREATE TABLE books (
  book_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  author_id INT,
  publisher_id INT,
  category_id INT,
  price DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  description TEXT,
  cover_image VARCHAR(255),
  FOREIGN KEY (author_id) REFERENCES authors(author_id) ON DELETE SET NULL,
  FOREIGN KEY (publisher_id) REFERENCES publishers(publisher_id) ON DELETE SET NULL,
  FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  total_amount DECIMAL(10,2) DEFAULT 0.00,
  status VARCHAR(20) DEFAULT 'Pending',
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS order_items;
CREATE TABLE order_items (
  order_item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  book_id INT,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS reviews;
CREATE TABLE reviews (
  review_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  book_id INT,
  rating INT,
  comment TEXT,
  review_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

-- sample data
INSERT INTO authors (author_name) VALUES ('George Orwell'), ('J.K. Rowling'), ('Haruki Murakami');
INSERT INTO publishers (publisher_name) VALUES ('Penguin'), ('Bloomsbury');
INSERT INTO categories (category_name) VALUES ('Fiction'), ('Fantasy'), ('Mystery');

INSERT INTO books (title, author_id, publisher_id, category_id, price, stock, description, cover_image) VALUES
('1984',1,1,1,499.00,50,'Dystopian classic.','1984.jpg'),
('Harry Potter and the Sorcerer''s Stone',2,2,2,699.00,100,'A boy discovers he is a wizard.','hp1.jpg'),
('Kafka on the Shore',3,1,1,599.00,30,'Surreal and moving.','kafka.jpg');

-- admin and sample customer, plain-text password '12345' (dev)
INSERT INTO users (full_name, email, password, role) VALUES
('Admin User','admin@bookhaven.com','12345','Admin'),
('Customer User','customer@bookhaven.com','12345','Customer');
