# 🚀 Web Development & E-Commerce Projects

Welcome to my comprehensive web development repository. This project documents my journey from learning frontend fundamentals to building a fully functional, database-driven **E-Commerce Platform**.

---

## 📂 Project Structure

This repository is divided into two main sections: Learning Modules and the Final Capstone Project.

### 🔹 Part 1: Frontend Development
This section contains various small-scale projects focusing on UI/UX and interactivity.
* **HTML5 & CSS3:** Semantic layouts, Flexbox, CSS Grid, and custom animations.
* **Bootstrap:** Rapid prototyping and mobile-first responsive design.
* **JavaScript & jQuery:** Dynamic DOM updates, event listeners, and client-side form validation.

### 🔹 Part 2: Final E-Commerce Project (`/ECommerce_Project`)
A robust, full-stack PHP application that allows users to browse products, vendors to manage inventory, and admins to oversee the entire ecosystem.

---

## 🛠️ Tech Stack

| Layer          | Technology                          |
| :------------- | :---------------------------------- |
| **Frontend** | HTML5, CSS3, JavaScript, jQuery     |
| **Styling** | Bootstrap 5                         |
| **Backend** | PHP                             |
| **Database** | MySQL                               |
| **Server** | InfinityFree (You can try on XAMPP)               |

---

## ✨ Key Features (E-Commerce)

### 👤 Customer Features
* **Product Catalog:** Browse products by category with real-time availability.
* **Shopping Cart:** Add, update, and remove items using persistent sessions.
* **Checkout Flow:** Secure multi-step process for order placement.
* **User Profile:** Track order history and manage account details.

### 🏪 Vendor Dashboard
* **Product Management:** Upload new products with images.
* **Order Tracking:** Monitor and manage customer orders specifically for their shop.
* **Inventory Control:** Edit or delete current listings.

### 🛡️ Admin Panel
* **Global Overview:** Dashboard for site-wide sales and user statistics.
* **Category Management:** Add or edit product categories.
* **Security:** Middleware-protected routes to ensure only admins can access the backend.
* **Review Moderation:** Manage customer feedback and contact messages.

---

## 🚀 Getting Started

To run the final project locally, follow these steps:

### 1. Prerequisites
Install a local server environment like **XAMPP** or **WAMP**.

### 2. Database Installation
1.  Open **phpMyAdmin**.
2.  Create a new database.
3.  Add DB Credentials in db.php.
4.  Import the file: `/ECommerce_Project/Database&DummyData.sql`.

### 3. File Configuration
1.  Copy the contents of the `/htdocs` folder into your server's root directory (e.g., `C:/xampp/htdocs/my-store`).
2.  Open `db.php` and configure your database connection:
    ```php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecommerce_db";
    ```

### 4. Accessing the Site
* **Main Store:** `http://localhost/index.php`
* **Admin Panel:** `http://localhost/admin/index.php`
* **Vendor Panel:** `http://localhost/vendor/index.php`

---

## 🖼️ Media Assets
The project includes a dedicated `/Assets` folder containing:
* **Banners:** Featured images for the home page slider.
* **Uploads:** Dynamically stored product images uploaded by vendors.
* **Libraries:** Local copies of Bootstrap and jQuery for offline development.

---

## 👨‍💻 Author
**Harsh Khandal** *Passionate Web Developer*
<br>
***Date : 1st Feb 2026***
