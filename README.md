# Simple E-Commerce Laravel

Simple E-Commerce application built with Laravel.  
This project is designed for learning and demonstration purposes, covering basic e-commerce features such as product management, orders, and payments.

---

## 🚀 Features

- Authentication
- Product management
- Order & order items
- Discount handling
- Payment & payment proof upload

---

## 🛠 Tech Stack

- **Backend**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Auth Sanctum
- **File Storage**: Local storage
- **API Response**: JSON Resource

---

## 📦 Requirements

- PHP >= 8.2
- Composer
- MySQL

---

## ⚙️ Installation

Clone repository:

```bash
git clone https://github.com/sukmagv/e-commerce.git
```

Install dependencies:

```bash
composer install
```

Copy environtment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

## 🔐 Environment Configuration

Edit .env file:

```bash
DB_DATABASE=e-commerce
DB_USERNAME=root
DB_PASSWORD=
```
Make sure database already exists.

---

## 🗄 Database Migration & Seeder

Run migration:

```bash
php artisan migrate
```

Run seeder:

```bash
php artisan db:seed
```

---

## ▶️ Running Project

```bash
php artisan serve
```

Application will run at:

```bash
http://127.0.0.1:8000
```

---

## 📂 Project Structure

```bash
app/
 ├── Http/
 |   ├── Controllers
 │   ├── Middleware
 │   ├── Requests
 │   └── Resources
 ├── Modules/
 │   ├── Auth
 │   ├── Order
 │   └── Product
 ├── Export
 ├── Observer
 ├── Policies
 ├── Rules
 └── Supports
database/
 ├── migrations
 └── seeders
routes
.env
```
