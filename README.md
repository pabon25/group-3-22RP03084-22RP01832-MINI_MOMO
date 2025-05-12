
## 👨‍💻 Developers
- MASENGESHO Pacifique - 22RP03084
- MBONIMPA ISHIMWE Theogene - 22RP01832

---
# 📱 MINI_MOMO – Mobile Money Simulation System

**MINI_MOMO** is a PHP-based mobile money simulation system that mimics core functionalities of Mobile Money (MoMo) transactions. It supports user and agent roles, balance management, secure transfers, and transaction history — all enhanced with simulated SMS notifications via Africa’s Talking.

---

## 🖥️ Requirements

- **XAMPP** (Apache + MySQL + PHP)  
- **Composer** (for dependency management)  
- **NGROK** (to expose your local server to the internet)  
- **Africa’s Talking** account & credentials  

---

## ⚙️ Setup Instructions

### 1. Clone the Project
```bash
git clone https://github.com/pabon25/group-3-22RP03084-22RP01832-MINI_MOMO.git

### ✅ 2. Move to XAMPP's `htdocs`
```bash
mv group-3-22RP03084-22RP01832-MINI_MOMO C:/xampp/htdocs/momo
```

### ✅ 3. Install Composer Dependencies
Make sure you have Composer installed: [https://getcomposer.org](https://getcomposer.org)

Then run:
```bash
cd C:/xampp/htdocs/momo
composer update
```

> **Default Admin Account**  
> After import, a default admin user is created:
> - **Username:** `admin`  
> - **Password:** `admin123`  


### ✅ 5. Update DB Connection
Edit `util.php` to match your database settings:
```php
    const HOST = "localhost";
    const DBNAME = "momo";
    const USERNAME = "root";
    const PASSWORD = "";
```

### ✅ 6. Start the Application
- Launch **Apache** in XAMPP
- Visit [http://localhost/momo](http://localhost/momo)

---
## 🚀 Features

### 👤 User & Agent Accounts
- Registration for customers and agents
- Secure login system
- Session-based authentication

### 💸 Send Money
- Transfer money between users and agents
- Real-time balance updates
- Validation for sufficient funds

### 💬 SMS Notifications
- Every transaction triggers a simulated SMS (via `Sms.php`)
- SMS includes amount, sender, and updated balance
- Used at the end of every transaction operation

### 📊 Transaction History
- Users and agents can view their individual transaction history
- Details include time, amount, type, and updated balance

### 🧑‍💼 Agent Features
- View and manage own transactions
- Approve Withdraw of User

---

## 📁 Project Structure

```

momo/
__Admin/
   ── login.php                # Login page
   ── logout.php               # End session
   __index.php                 #Admin Dashboard
── Sms.php                  # Simulates SMS sending
── menu.php                 # Dashboard after login
──Util                      #Contains Unchangeable values
──index.php                 #Contains Objects corrected to make functionality (it is one to be used to access Our USSD Application)
── DB/
   └── mini_momo.sql        # SQL database file
── vendor/                  # Composer dependencies
└─ README.md                # This file
```

## 📜 License
This project is for educational purposes.
