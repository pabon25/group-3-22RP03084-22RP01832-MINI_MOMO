
# 📱 MINI_MOMO - Mobile Money Simulation System

**MINI_MOMO** is a PHP-based mobile money simulation system that mimics core functionalities of Mobile Money (MoMo) transactions. It supports user and agent roles, balance management, secure transfers, and transaction history — all enhanced with simulated SMS notifications.

---
## ⚙️ Setup Instructions

### ✅ 1. Clone the Project
```bash
git clone https://github.com/pabon25/group-3-22RP03084-22RP01832-MINI_MOMO.git
```

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

### ✅ 4. Import the Database
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Create a new database named `momo`
3. Go to **Import**
4. Choose the file: `DB/momo.sql`
5. Click **Go**

### ✅ 5. Update DB Connection
Edit `connect.php` to match your database settings:
```php
$conn = new mysqli("localhost", "root", "", "mini_momo");
```

### ✅ 6. Start the Application
- Launch **Apache** in XAMPP
- Visit [http://localhost/momo](http://localhost/momo)


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
- Agents function like users but can be extended for cash-in/cash-out
- View and manage own transactions

---

## 📁 Project Structure

```
momo/
├── Sms.php                  # Simulates SMS sending
├── connect.php              # Database connection setup
├── register.php             # New user/agent registration
├── login.php                # Login page
├── send.php                 # Transfer money interface
├── transactions.php         # Transaction history display
├── logout.php               # End session
├── menu.php                 # Dashboard after login
├── DB/
│   └── mini_momo.sql        # SQL database file
├── vendor/                  # Composer dependencies
└── README.md                # This file
```

---
---

## 👨‍💻 Authors
- MASENGESHO Pacifique - 22RP03084
- MBONIMPA ISHIMWE Theogene - 22RP01832

---

## 📜 License
This project is for educational purposes.
