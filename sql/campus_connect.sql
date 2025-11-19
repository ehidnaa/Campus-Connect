-- 🌸 Hi! This is my little database setup for the Campus Connect project!
-- 🌸 I'm creating everything here so PHP can fetch real data later 💖

-- 💗 Create the database (just in case it doesn't exist yet)
CREATE DATABASE IF NOT EXISTS campus_connect
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- 💗 Switch into my database so all tables go here
USE campus_connect;





/* ─────────────── 🌼 USERS TABLE 🌼 ───────────────
   This will store all my students! (and one admin hehe)
   Passwords will be hashed in PHP later ✨
*/
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,    -- will be hashed, promise! 🤞
  role VARCHAR(50) NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;





/* ─────────────── 🌼 EVENTS TABLE 🌼 ───────────────
   All campus events we want students to see!
*/
CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  date DATE NOT NULL,
  location VARCHAR(255) NOT NULL
) ENGINE=InnoDB;





/* ─────────────── 🌼 REGISTRATIONS 🌼 ───────────────
   This connects students to events (many-to-many).
   Basically: Who signed up for what? 🌟
*/
CREATE TABLE IF NOT EXISTS registrations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reg_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB;





/* ─────────────── 🌼 MERCH TABLE 🌼 ───────────────
   Our cute little campus shop! Hoodies, mugs, stickers… ✨
*/
CREATE TABLE IF NOT EXISTS merch (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) DEFAULT NULL   -- will store the image filename 🛍️
) ENGINE=InnoDB;





/* ─────────────── 🌼 ORDERS TABLE 🌼 ───────────────
   When students buy merch, the order goes here ✨
*/
CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;





/* ─────────────── 🌼 REVIEWS TABLE 🌼 ───────────────
   Students can leave reviews about events 💬✨
*/
CREATE TABLE IF NOT EXISTS reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  rating INT NOT NULL,       -- from 1 to 5 probably ⭐
  text TEXT,                 -- optional comment
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB;





/* ─────────────── 🌸 SAMPLE DATA 🌸 ───────────────
   Just to make my pages look alive when I start coding the frontend 💖
*/

-- 💕 admin account (password will be hashed later!)
INSERT INTO users (name, email, password, role)
VALUES ('Admin User', 'admin@campus.test', 'TEMP_PASSWORD', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- 🌼 two example events for testing
INSERT INTO events (title, description, date, location) VALUES
('Welcome Fair', 'Clubs, societies and freebies for first year students.', '2025-09-20', 'ATU Galway Main Hall'),
('Exam Prep Workshop', 'Tips and tricks to survive exam week.', '2025-12-10', 'Room B102');

-- 🛍️ some cute merch items
INSERT INTO merch (name, price, image) VALUES
('Campus Hoodie', 39.99, 'hoodie.jpg'),
('ATU Mug', 9.99, 'mug.jpg'),
('Sticker Pack', 3.50, 'stickers.jpg');