# HK Store - Multi-Vendor E-commerce Platform

A custom-built PHP e-commerce system featuring distinct portals for Administrators, Vendors, and Customers. This project serves as a comprehensive demonstration of full-stack PHP development, focusing on session management, secure authentication, and relational database architecture.

## 🚀 Live Demo

* **Primary Mirror:** [harshkhandal.me](https://harshkhandal.me/Tutedude-Web/Final_Project/)
* **InfinityFree Mirror:** [ecoproject.infinityfree.me](http://ecoproject.infinityfree.me/)

---

## 🔑 Test Credentials

You can use the following accounts to test the different access levels and functionalities of the platform.  
**Default Password for all accounts:** `password`

### 👤 Customer Accounts
* `customer@ecoproject.com`
* `customer1@ecoproject.com`
* `customer2@ecoproject.com`
* `customer3@ecoproject.com`
* `customer4@ecoproject.com`

### 🏪 Vendor Accounts
* `vendor1@hkstore.com`
* `vendor2@hkstore.com`
* `vendor3@hkstore.com`

### 🛠 Admin Account
* `admin@ecoproject.com`

---

## 🌟 Key Features

### User Portals
* **Customer Dashboard:** Personal profile management, real-time order history tracking, and itemized digital receipts.
* **Vendor Panel:** Dedicated inventory management system allowing vendors to process orders and manage products specific to their shop.
* **Admin Console:** Global oversight featuring revenue analytics, user role permissions, inventory control, and a centralized contact inquiry hub.

### Technical Implementation
* **Secure Authentication:** Password protection using Bcrypt hashing and logical session-based routing to prevent unauthorized access.
* **Database Architecture:** Relational mapping that automatically links shopping cart sessions to permanent order and item records.
* **Clean UI:** Responsive design built with Bootstrap 5, featuring interactive elements powered by JavaScript and jQuery.



## 🛠 Tech Stack
* **Backend:** PHP (Procedural)
* **Database:** MySQL
* **Frontend:** Bootstrap 5, CSS3, JavaScript (jQuery)
* **Hosting:** InfinityFree

## 📂 Project Structure
* `/admin` - Administrative management logic and dashboard.
* `/vendor` - Vendor portals and shop-specific management.
* `/includes` - Core reusable components (Header, Footer, Session Logic).
* `/Assets` - Product media and site assets.
* `db.php` - Database connection and configuration.
* `Database&DummyData.sql` - Complete database structure and dummy data for easy replication.

## ⚙️ Setup Instructions

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/your-username/hk-store.git
2. **Database Configuration:**
    - Create a MySQL database (e.g., hk_store).

    - Import the Database&DummyData.sql file to generate tables and dummy data.

3. **Deployment:**
    - Move the contents of the htdocs folder to your local server directory (e.g., XAMPP htdocs) or your live hosting public directory.

## 📝 Disclaimer

This project was developed for educational purposes to test and showcase full-stack PHP development skills.