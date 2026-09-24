# 🍽️ Webtech Online Food Ordering System

A PHP-based web application designed for **Restaurant Managers (Role 2)** to manage daily restaurant operations, including menu inventory, live orders, customer feedback, complaints, discounts, and sales analytics.

The system follows the **Model-View-Controller (MVC)** architecture and features a modern **Purple & White** user interface.

---

## 🚀 Features

* 🔐 **Authentication & Access Control**

  * Secure login system for Restaurant Managers
  * Role-based access control restricted to Role 2
  * Session validation for protected operations

* 📊 **Analytics Dashboard**

  * Total revenue overview
  * Daily order statistics
  * Customer feedback summaries
  * Dynamic sales and order metrics

* 📦 **Order Management**

  * Track incoming and ongoing orders
  * Update order status
  * Monitor completed and cancelled orders
  * Structured order lifecycle management

* 🍔 **Menu Management**

  * Create new menu items
  * Edit existing items
  * Toggle item availability
  * Remove menu items

* 🏷️ **Discounts & Promotions**

  * Create promotional codes
  * Manage percentage-based discounts
  * Support seasonal promotional offers

* ⭐ **Reviews & Complaints**

  * Monitor customer ratings and reviews
  * Review customer feedback
  * Handle customer complaints and service issues

---

## 🛠️ Technologies Used

| Technology           | Purpose                                          |
| -------------------- | ------------------------------------------------ |
| **PHP**              | Backend development                              |
| **MVC Architecture** | Application structure and separation of concerns |
| **MySQL**            | Database management                              |
| **HTML5**            | Page structure                                   |
| **CSS3**             | Styling and responsive interface                 |
| **JavaScript**       | Client-side interactions                         |
| **XAMPP / WAMP**     | Local development environment                    |
| **Git & GitHub**     | Version control and collaboration                |

---

## 🏗️ System Architecture

The application follows the **Model-View-Controller (MVC)** design pattern.

### Model

Responsible for:

* Database interactions
* SQL queries
* Data processing
* Business-related database operations

### View

Responsible for:

* User interface
* Dashboard pages
* Forms and tables
* Responsive presentation

### Controller

Responsible for:

* Request handling
* Input validation
* Application logic
* Connecting models with views
* Access control

This separation helps keep the application organized, maintainable, and easier to extend.

---

## 🔄 Methodology

### 1. Role-Based Access Control

The system restricts restaurant management functionality to authenticated **Role 2 users**.

Protected operations verify the user's session and role before allowing access.

### 2. Request Routing

User requests are handled through the MVC structure:

```text
User Request
     ↓
Controller
     ↓
Model ↔ Database
     ↓
Controller
     ↓
View
     ↓
User Interface
```

### 3. Order Processing

Orders follow a structured lifecycle:

```text
Pending
   ↓
Preparing
   ↓
Out for Delivery
   ↓
Completed
```

Orders may also be marked as:

```text
Cancelled
```

### 4. Sales & Feedback Processing

The dashboard dynamically processes order and sales information to provide managers with operational summaries.

Customer reviews and complaints are also collected for managerial monitoring and service improvement.

---

## 📂 Project Structure

```text
Webtech_Online_Food_ordering_System-main/
│
├── app/
│   ├── config/
│   │   └── Configuration settings
│   │
│   ├── controllers/
│   │   └── Request handling and controller logic
│   │
│   ├── core/
│   │   └── Authentication and database classes
│   │
│   ├── models/
│   │   └── Database interaction and business logic
│   │
│   └── views/
│       └── UI templates and reusable components
│
├── database/
│   └── Role 2 SQL schema and seed files
│
├── public/
│   ├── api/
│   │   └── REST/JSON endpoints
│   │
│   ├── assets/
│   │   ├── CSS
│   │   ├── JavaScript
│   │   └── Media
│   │
│   └── index.php
│       └── Application entry point
│
└── README.md
```

---

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone REPOSITORY_URL
cd Webtech_Online_Food_ordering_System-main
```

### 2. Configure the Local Server

Move the project directory into your local server's web root.

**For XAMPP:**

```text
C:\xampp\htdocs\
```

**For WAMP:**

```text
C:\wamp64\www\
```

### 3. Create the Database

Open **phpMyAdmin**:

```text
http://localhost/phpmyadmin
```

Create a database named:

```text
food_ordering_db
```

Then import:

```text
database/role2_restaurant_manager.sql
```

### 4. Configure Database Credentials

Open:

```text
app/config/config.php
```

Verify the database configuration:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_ordering_db');
```

Update the credentials if your local MySQL configuration is different.

---

## ▶️ Usage

### 1. Start the Server

Open your **XAMPP/WAMP Control Panel** and start:

* Apache
* MySQL

### 2. Open the Application

Navigate to:

```text
http://localhost/Webtech_Online_Food_ordering_System-main/public/
```

### 3. Login

Use the available **Restaurant Manager (Role 2)** credentials to access the management portal.

---

## 🔐 Security & Data Handling

The application includes several measures to protect manager-level functionality and database operations.

### Session Security

Manager sessions are validated for protected endpoints to prevent unauthorized access.

### SQL Injection Prevention

Database interactions use **prepared statements** through the application's database layer.

### Role Verification

Operations restricted to Restaurant Managers verify the user's role before allowing access.

Unauthorized users are prevented from accessing protected management functionality.

---

## 🎯 Project Objective

The objective of this project is to develop an intuitive and reliable restaurant management portal that helps managers:

* Monitor restaurant operations
* Manage menu inventory
* Track and update orders
* Monitor sales performance
* Manage discounts and promotions
* Review customer feedback
* Handle customer complaints

The project demonstrates practical implementation of **full-stack web development, MVC architecture, database management, authentication, and role-based access control**.

---

## 📌 Future Improvements

* 🔔 Real-time WebSocket notifications for new incoming orders
* 📄 Automated daily sales PDF report generation
* 📈 Advanced sales trend visualizations
* 💳 Integration with third-party payment gateways
* ⚡ Enhanced real-time order monitoring

---

## 👨‍💻 Author

Anannya Tithi | 
Computer Science & Engineering
Bangladesh

---

## 📚 Project Type

**Web Technology Project**

**Domain:** Restaurant Management & Online Food Ordering
**Architecture:** Model-View-Controller (MVC)
**Backend:** PHP
**Database:** MySQL
