CREATE DATABASE IF NOT EXISTS oneofone CHARACTER SET utf8mb4;
USE oneofone;
CREATE TABLE products(
 id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(120) NOT NULL, category VARCHAR(40) NOT NULL,
 size VARCHAR(20) NOT NULL, item_condition VARCHAR(30) NOT NULL DEFAULT 'Good', price INT NOT NULL,
 descr TEXT, image VARCHAR(80) NULL, status ENUM('available','reserved','sold') NOT NULL DEFAULT 'available',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE orders(
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80), phone VARCHAR(20), zone VARCHAR(60), address VARCHAR(255),
 fee INT DEFAULT 0, total INT, status ENUM('pending','paid','cancelled') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE order_items(order_id INT, product_id INT, price INT, PRIMARY KEY(order_id,product_id));
INSERT INTO products(title,category,size,item_condition,price,descr) VALUES
('Oversized denim jacket','Jackets','L','Great',1800,'Heavy washed denim, boxy fit.'),
('Vintage Nike crewneck','Sweatshirts','M','Good',1500,'Faded grey, small cuff wear.'),
('Pleated midi skirt','Skirts','S','Like new',1200,'Black, lined, elastic waist.'),
('Carhartt work pants','Trousers','32','Good',2200,'Straight leg, broken-in canvas.');
