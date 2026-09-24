# 🍽️ Online Food Ordering System — Restaurant Manager Portal

A comprehensive web application designed for restaurant managers to oversee menu inventory, track live orders, resolve customer complaints, review feedback, and analyze sales performance. Built with a clean **Purple & White** aesthetic using the MVC architecture.

---

## ✨ Features

* **🔐 Authentication & Access Control**: Secure login system locked to Role 2 (Restaurant Manager) with session validation.


* **📊 Analytics Dashboard**: High-level metrics for total revenue, daily orders, and customer feedback summaries.


* **📦 Order Management**: Live tracking and status updates across incoming, ongoing, and completed orders.


* **🍕 Menu Management**: Interface to create, edit, toggle availability, and remove menu items.


* **🏷️ Discounts & Promotions**: Manage promotional codes, percentage discounts, and seasonal offers.


* **💬 Reviews & Complaints**: Monitor customer ratings and address service issues directly.



---

## 🛠️ Tech Stack

* **Backend**: PHP (MVC Pattern)


* **Database**: MySQL


* **Frontend**: HTML5, CSS3 (Purple & White UI Theme), JavaScript


* **API**: REST/JSON endpoints for real-time order updates



---

## 📂 Project Structure

```text
Webtech_Online_Food_ordering_System-main/
├── app/
│   ├── config/          # System configuration settings[cite: 2]
│   ├── controllers/     # Request routing & controller logic[cite: 2]
│   ├── core/            # Core framework classes (Auth, Database)[cite: 2]
│   ├── models/          # Database interaction models[cite: 2]
│   └── views/           # UI templates & partials[cite: 2]
├── database/            # Role 2 SQL schema & seed files[cite: 2]
├── public/
│   ├── api/             # API endpoints (e.g., orders.php)[cite: 2]
│   ├── assets/          # CSS stylesheets, scripts, and media assets[cite: 2]
│   └── index.php        # Application entry point
└── README.md

```

---

## 🚀 Setup & Installation

1. **Clone/Place Directory**:
Place the project folder into your local web server root (e.g., `htdocs` for XAMPP or `www` for WAMP/LAMP).
2. **Database Import**:
* Open phpMyAdmin or your MySQL client.
* Create a database (e.g., `food_ordering_db`).
* Import the SQL schema from `database/role2_restaurant_manager.sql`.




3. **Configure Settings**:
* Open `app/config/config.php` and verify your database connection credentials (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).






4. **Run Project**:
* Start your Apache and MySQL services.
* Navigate to `http://localhost/Webtech_Online_Food_ordering_System-main/public/` in your browser.
