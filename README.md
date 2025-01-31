# Event Management System

## Overview
The **Event Management System** is a PHP-based web application designed to facilitate event creation, user registration, and attendance tracking. It provides an admin panel for managing events and users, while regular users can browse events and register for participation.

## Features
### **Admin Panel**
- Dashboard displaying total events and attendees.
- CRUD operations for events (Create, Read, Update, Delete).
- Pagination, sorting, and filtering for event listings.
- CSV export for attendee lists of specific events.
- Admin management (ability to create new admin users).

### **User Features**
- User authentication (login, registration, and session handling).
- Event browsing with detailed descriptions.
- Event registration (with validation to prevent multiple registrations).
- Registration button disabled if already registered.
- Restriction preventing admin accounts from registering for events.

### **Event Management**
- Event creation with cover and thumbnail images.
- Image upload handling with automatic deletion of old images.
- Event registration tracking.
- Event capacity enforcement (limiting attendees per event).

### **Security Features**
- Secure password hashing using `password_hash()`.
- Role-based authentication (admin and user access control).
- Session-based authentication and redirection.
- Preventing duplicate admin creation and duplicate event registrations.

## Installation Instructions
### **1. Clone the Repository**
```sh
    git clone https://github.com/na0260/event-managment-php.git
    cd event-managment-php
```

### **2. Configure the Database**
- Import the provided SQL file (`database.sql`) into MySQL.
- Update database credentials in `config/database.php`:

```php
    $host = "localhost";
    $dbname = "event_management_php";
    $username = "root";
    $password = "";
```

### **3. Start the Local Server**
```sh
    php -S localhost:8000
```
Visit `http://localhost:8000` in your browser.

## Admin Credentials
- **Email:** `admin@mail.com`
- **Password:** `admin123`

## User Credentials
- **Email:** `user@mail.com`
- **Password:** `user123`

## Folder Structure
```
|-- admin/             # Admin panel
|-- assets/            # Stylesheets and JavaScript
|-- auth/              # Login & Registration system
|-- config/            # Database connection
|-- events/            # Event management pages
|-- includes/          # Common reusable components (e.g., navbar)
|-- index.php          # Homepage
|-- README.md          # Documentation
|-- uploads/           # Stores event images
```

## Contribution & Support
For contributions, submit a pull request or report issues on GitHub. For support, contact the project owner.

---
### Developed by:
- Md. Nur Ahmed
- Email: nurahmed.contact@gmail.com

