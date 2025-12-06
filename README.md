# 🌸 Campus Connect 💗

A small and cute student platform for events, merch, reviews and orders.  
Built with PHP, MySQL, PDO, Fetch API and Bootstrap — clean and simple ✨

## 💕 Main Features

### 🌼 Users
- Registration with validation  
- Password hashing  
- Login using sessions  
- Admin role support  

### 🌸 Events
- Events loaded from MySQL  
- Register via Fetch (JSON + HTML)  
- Leave reviews ⭐  
- View all reviews  

### 🛍 Merch + Cart
- Merch items from database  
- Add to cart using Fetch  
- Cart stored in sessions  
- Checkout creates an order  

### ⚙️ Admin
- Dashboard with stats  
- Recent registrations + orders  

### 💗 Cookies + Favicon
- Light/Dark theme saved in cookie  
- Custom favicon included  

## 🌺 Folder Structure
api/ — Fetch API backend  
css/ — styling  
js/ — scripts  
images/ — icons + favicon  
sql/ — database schema  
*.php — main pages  

## 🌸 Setup
1. Move project to `www/`  
2. Create database `campus_connect`  
3. Import `sql/campus_connect.sql`  
4. Open: http://localhost/campus_connect/

## 💛 Admin Access
UPDATE users SET role='admin' WHERE id=1;

## ✔ Requirements
All 13 module requirements completed 💗🌸✨

Campus Connect — project for Dynamic Web Development Framework.
