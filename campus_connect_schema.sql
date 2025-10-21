
-- Campus Connect Database Schema (MySQL 8.x)
-- Engine/charset
CREATE DATABASE IF NOT EXISTS campus_connect
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE campus_connect;

-- Users: students and admins
CREATE TABLE users (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role          ENUM('student','admin') NOT NULL DEFAULT 'student',
  first_name    VARCHAR(100) NOT NULL,
  last_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Events
CREATE TABLE events (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(200) NOT NULL,
  description  TEXT,
  location     VARCHAR(255) NOT NULL,
  starts_at    DATETIME NOT NULL,
  ends_at      DATETIME NULL,
  capacity     INT UNSIGNED NULL,
  created_by   BIGINT UNSIGNED NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_events_created_by FOREIGN KEY (created_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_events_starts_at (starts_at),
  INDEX idx_events_location (location)
) ENGINE=InnoDB;

-- Registrations (user <-> event; 1 user can register once per event)
CREATE TABLE registrations (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      BIGINT UNSIGNED NOT NULL,
  event_id     BIGINT UNSIGNED NOT NULL,
  registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status       ENUM('registered','cancelled','attended','no_show') NOT NULL DEFAULT 'registered',
  UNIQUE KEY uq_user_event (user_id, event_id),
  CONSTRAINT fk_reg_user  FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX idx_reg_event (event_id),
  INDEX idx_reg_user (user_id)
) ENGINE=InnoDB;

-- Merchandise (event-related products)
CREATE TABLE merch (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id     BIGINT UNSIGNED NULL, -- optional link to specific event
  name         VARCHAR(200) NOT NULL,
  description  TEXT,
  image_url    VARCHAR(500) NULL,
  price_cents  INT UNSIGNED NOT NULL, -- store price in cents to avoid float
  stock_qty    INT NOT NULL DEFAULT 0,
  is_active    TINYINT(1) NOT NULL DEFAULT 1,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_merch_event FOREIGN KEY (event_id) REFERENCES events(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_merch_event (event_id),
  INDEX idx_merch_active (is_active)
) ENGINE=InnoDB;

-- Orders (header). Each order can contain multiple merch items.
CREATE TABLE orders (
  id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id        BIGINT UNSIGNED NOT NULL,
  status         ENUM('pending','paid','cancelled','refunded') NOT NULL DEFAULT 'pending',
  total_cents    INT UNSIGNED NOT NULL DEFAULT 0,
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (status)
) ENGINE=InnoDB;

-- Order items (line items) - normalized detail for orders
CREATE TABLE order_items (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id    BIGINT UNSIGNED NOT NULL,
  merch_id    BIGINT UNSIGNED NOT NULL,
  quantity    INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price_cents INT UNSIGNED NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_items_merch FOREIGN KEY (merch_id) REFERENCES merch(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uq_order_merch (order_id, merch_id)
) ENGINE=InnoDB;

-- Reviews: can target an event OR a merch item (polymorphic via nullable FKs)
CREATE TABLE reviews (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      BIGINT UNSIGNED NOT NULL,
  event_id     BIGINT UNSIGNED NULL,
  merch_id     BIGINT UNSIGNED NULL,
  rating       TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment      TEXT,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_user  FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_reviews_event FOREIGN KEY (event_id) REFERENCES events(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_reviews_merch FOREIGN KEY (merch_id) REFERENCES merch(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  -- ensure at least one target is provided
  CONSTRAINT chk_review_target CHECK ((event_id IS NOT NULL) OR (merch_id IS NOT NULL)),
  INDEX idx_reviews_event (event_id),
  INDEX idx_reviews_merch (merch_id),
  INDEX idx_reviews_user (user_id)
) ENGINE=InnoDB;

-- OPTIONAL: Favorites (users can save events)
CREATE TABLE favorites (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    BIGINT UNSIGNED NOT NULL,
  event_id   BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_fav (user_id, event_id),
  CONSTRAINT fk_fav_user  FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_fav_event FOREIGN KEY (event_id) REFERENCES events(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Suggested minimal seed data for demo
INSERT INTO users (role, first_name, last_name, email, password_hash) VALUES
('admin','Campus','Admin','admin@campusconnect.test','$2y$12$examplehash'),
('student','Mariia','Chalyk','mariia@example.com','$2y$12$examplehash');

INSERT INTO events (title, description, location, starts_at, ends_at, capacity, created_by) VALUES
('Welcome Fair','Meet societies and clubs.','ATU Main Hall','2025-10-28 10:00:00','2025-10-28 15:00:00',300,1);

INSERT INTO merch (event_id, name, description, price_cents, stock_qty, image_url) VALUES
(1,'Campus Hoodie','Soft cotton hoodie with ATU crest.',3999,50,NULL),
(1,'Event Badge','Limited edition enamel badge.',899,200,NULL);

INSERT INTO registrations (user_id, event_id, status) VALUES (2,1,'registered');

INSERT INTO orders (user_id, status, total_cents) VALUES (2,'paid',4898);
INSERT INTO order_items (order_id, merch_id, quantity, unit_price_cents) VALUES
(1,1,1,3999),
(1,2,1,899);

INSERT INTO reviews (user_id, event_id, rating, comment) VALUES (2,1,5,'Great event!');
