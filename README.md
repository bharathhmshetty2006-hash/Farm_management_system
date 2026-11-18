# Farm Management System 🌾
> **VTU 5th Semester Mini Project** | Bachelor of Engineering in Computer Science & Engineering  
> **Institution:** Govt Engineering College, Hassan – 573211  
> **Affiliated to:** Visvesvaraya Technological University (VTU), Belagavi  
> **Guide:** prof. Gowreesha H B, Department of CS&E  

---

## 👥 Student Project Team
| Sl. No. | Student Name | USN |
| 1 | **Bharath H M** | `4GH24CS020` |

---

## 🛠️ Project Overview & Architecture
The **Farm Management System** is a web-based application built using **PHP (PDO)** and **MySQL** database on **Bootstrap 5** frontend framework. It digitizes farm recordkeeping, eliminating paper registers and providing real-time data analysis across 8 key modules:

1. **Farmer Management:** Digital registry for farmer profiles, contact numbers, and location details.
2. **Crop Management:** Crop species tracking, seasonal cycles (*Kharif, Rabi, Zaid, Annual*), planting & harvesting dates.
3. **Seed Management:** Variety details, quantities, suppliers, costs, and auto-expense integration.
4. **Labour Management:** Worker records, working days, daily wage rates, auto-calculated total wages, and auto-expense integration.
5. **Expense Management:** Category-based expense tracking (*Seeds, Labour, Transportation, Other*).
6. **Equipment Management:** Machinery & tool inventory (*Tractors, Tillage, Irrigation, Sprayers*), purchase dates, capital expenditure.
7. **Production Management:** Harvest yield metrics in Kilograms (kg) and grain quality notes.
8. **Sales Management:** Crop sales transactions, buyer/market details, quantity sold, unit price, total revenue calculation, and yield warning checks.
9. **Profit & Loss Analysis Report:** Real-time financial reports calculating total expenses, total sales revenue, net profit/loss per crop, and visual yield alert indicators.

---

## 💻 Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5.3 (CDN), FontAwesome 6 (Icons)
- **Backend:** PHP 7.4 / 8.x (PDO Extension, Prepared Statements)
- **Database:** MySQL 5.7+ / MariaDB (`farm_db`)
- **Web Server:** XAMPP / WAMP / Apache local host

---

## 🚀 Step-by-Step Setup Guide

### Step 1: Clone or Copy Project Folder
Copy the entire `farm_management` folder into your XAMPP `htdocs` directory:
```text
C:\xampp\htdocs\farm_management\
```

### Step 2: Start XAMPP Control Panel
1. Open **XAMPP Control Panel**.
2. Start the **Apache** and **MySQL** services.
3. Confirm that both services turn green.

### Step 3: Import MySQL Database (`farm_db`)
1. Open your browser and navigate to `http://localhost/phpmyadmin/`.
2. Click on **Databases** -> Create a database named `farm_db`.
3. Click on the newly created `farm_db` database, then select the **Import** tab.
4. Click **Choose File** and select `farm_management/database/farm_db.sql`.
5. Click **Go** at the bottom to execute the SQL script.

### Step 4: Access the Application
Open your browser and visit:
```text
http://localhost/farm_management/
```
---

## 📁 Directory Structure
```text
farm_management/
├── index.php                 # Entry point (redirects to dashboard/login)
├── login.php                 # User authentication with password hashing & session management
├── logout.php                # Session termination handler
├── dashboard.php             # Main dashboard with statistics, metrics cards, recent tables & warnings
├── config/
│   └── db.php                # PDO database connection & global helper functions
├── includes/
│   ├── header.php            # Head meta, Bootstrap/FontAwesome CDNs, navigation bar
│   ├── sidebar.php           # Sidebar navigation for 8 modules + reports
│   └── footer.php            # Footer elements, Bootstrap bundle JS & script.js
├── assets/
│   ├── css/
│   │   └── style.css         # Modern agricultural themed CSS styles
│   └── js/
│       └── script.js         # Phone pattern check, date validations, wage/sale auto-calculator
├── modules/
│   ├── farmer/               # list.php, add.php, edit.php, delete.php
│   ├── crop/                 # list.php, add.php, edit.php, delete.php
│   ├── seed/                 # list.php, add.php, edit.php, delete.php
│   ├── labour/               # list.php, add.php, edit.php, delete.php
│   ├── expense/              # list.php, add.php, edit.php, delete.php
│   ├── equipment/            # list.php, add.php, edit.php, delete.php
│   ├── production/           # list.php, add.php, edit.php, delete.php
│   └── sales/                # list.php, add.php, edit.php, delete.php
├── reports/
│   └── profit_loss.php       # Crop-wise financial report & print statement
└── database/
    └── farm_db.sql           # Schema DDL & rich pre-populated sample data
```

---

## 🛡️ Security & Best Practices Implemented
- **SQL Injection Prevention:** All SQL queries use PDO Prepared Statements (`$stmt->prepare()` & `$stmt->execute()`).
- **XSS Sanitization:** User input rendered using `htmlspecialchars()`.
- **Cascading Foreign Keys:** Deleting a farmer or crop safely cleans up associated child records via `ON DELETE CASCADE`.
- **Client & Server Validation:** Double-layered checks for phone numbers (10 digits), harvest dates, and positive values.
