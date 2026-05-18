Role 2 - Restaurant Manager Only

This folder is independent. It does not depend on Role 3 or any shared_database folder.

XAMPP Run Steps:
1. Copy role2_restaurant_manager folder into C:\xampp\htdocs
2. Start Apache and MySQL from XAMPP Control Panel
3. Open phpMyAdmin
4. Import database/role2_restaurant_manager.sql
5. Visit:
   http://localhost/role2_restaurant_manager/public/index.php

Approved Demo Login:
Email: manager@foodhub.test
Password: password123

Pending Approval Demo Login:
Email: pending@foodhub.test
Password: password123
This account cannot log in because its restaurant is not admin-approved yet.

Completed Role 2 Features:
- Restaurant registration request with admin approval gate
- Manager login only after restaurant approval
- Restaurant profile management with logo upload
- Open/closed toggle, cuisine, address, city, opening hours, delivery radius
- Menu category create, rename, reorder, delete
- Menu item add, edit, delete, category, price, description, image upload, availability toggle
- AJAX menu availability toggle
- Real-time AJAX active/incoming orders refresh
- Accept/reject orders and update status to Preparing and Ready for Pickup
- Active orders dashboard grouped by status
- Full order history with customer, items, total, and delivery status
- Customer reviews with public manager reply
- Sales analytics: daily, weekly, monthly orders/revenue, average order value, most ordered items
- Discount campaign create, activate/deactivate, delete, validity dates, performance count
- Complaints related to the restaurant

AJAX Feature:
Dashboard active orders refresh automatically every 10 seconds using XMLHttpRequest through public/api/orders.php.
Menu availability toggle also uses AJAX.

MVC Structure:
app/controllers
app/models
app/views
app/core
public/assets
public/api
public/uploads
database


PURPLE/WHITE VISUAL UPGRADE
- Added custom FoodHub Manager logo files in public/assets/images/.
- Added purple-and-white hero/banner illustrations and demo food item pictures.
- Updated CSS theme with purple gradients, white cards, rounded panels, improved nav, buttons, tables, forms, and login/register pages.
- Demo SQL now assigns default restaurant logo and menu item images.

This project follows MVC architecture.
Restaurant Manager can manage profile, menu, discounts, orders, reviews, analytics, and complaints.
AJAX is used for real-time order updates and menu availability toggle.