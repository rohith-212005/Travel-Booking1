# Travel Booking Platform

A comprehensive web-based platform for booking flights, hotels, trains, buses, and cabs with a professional login system and payment integration.

🌐 Live Demo

👉 http://56.228.26.179/

## 📁 Project Structure

This project is organized into a clean directory structure for better maintainability:

```
/
├── backend/          # PHP logic, database connections, and session handling
├── frontend/         # HTML pages and templates
├── styles/           # CSS stylesheets for all modules
├── assets/           # Static assets
│   ├── js/          # JavaScript logic and API integrations
│   └── img/         # Images and icons
├── index.php         # Root entry point (redirects to login)
├── .gitignore        # Git ignore rules
└── README.md         # Project documentation
```

## 🚀 Features

- **Multi-Module Booking**: Dedicated sections for Flights, Hotels, Trains, Buses, and Cabs.
- **Secure Authentication**: User registration and login with hashed passwords.
- **Dynamic Search**: Interactive search forms with real-time feedback (simulated).
- **Payment Integration**: Complete payment flow with Card and UPI options.
- **Ticket Generation**: Automated ticket generation with QR code support.
- **Booking History**: Users can track their past bookings and view tickets.
- **Responsive Design**: Optimized for both Desktop and Mobile devices.

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL (PDO)
- **Icons**: Font Awesome 6.0
- **Animations**: Animate.css

## ⚙️ Installation & Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/[your-username]/travel-booking.git
   ```

2. **Database Configuration**:
   - Update `backend/db-connection.php` with your database credentials (host, dbname, username, password).
   - Import the required SQL tables into your MySQL database.

3. **Deploy**:
   - Upload all files to your web server (compatible with PHP and MySQL).
   - The site is accessible via the root URL.

---
© 2024 Travel Booking. All rights reserved.
