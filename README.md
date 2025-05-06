# Smart Budget Manager 💰

A powerful Laravel-based personal finance application to track budgets, manage transactions, and gain insights into your spending habits.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-339933?style=for-the-badge&logo=nodedotjs&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

## Table of Contents

-   [Features](#-features)
-   [Quick Start](#-quick-start)
-   [Installation Guide](#-installation-guide)
-   [Development](#-development)
-   [Production Deployment](#-production-deployment)
-   [Troubleshooting](#-troubleshooting)
-   [Contributing](#-contributing)
-   [License](#-license)

## ✨ Features

-   📊 Visual budget tracking
-   💸 Transaction management
-   📈 Spending analytics
-   🔐 Secure authentication
-   📱 Responsive design
-   📤 CSV/Excel export

## 🚀 Quick Start

### Prerequisites

-   **PHP** ≥ 8.1
-   **Composer** (latest version)
-   **Node.js** ≥ 16.x + **npm**
-   **MySQL** ≥ 5.7 or **PostgreSQL** ≥ 10
-   **Git**

## 🛠 Installation Guide

### 1. Clone & Setup

```bash
git clone https://github.com/HassanAbdelhamed22/Smart-Budget-Manager.git
cd Smart-Budget-Manager
```

### 2. Install Dependencies

1. PHP dependencies:

```bash
composer install
```

2. Frontend assets:

```bash
npm install
npm run build
```

### 3. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` with your settings:

```env
APP_NAME="Smart Budget Manager"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_budget_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Keys & Setup DB

1. Generate application key:

```bash
php artisan key:generate
```

2. Create database (MySQL example):

```bash
mysql -u root -p -e "CREATE DATABASE smart_budget_manager;"
```

3. Run migrations:

```bash
php artisan migrate
```

### 5. Start Server

```bash
php artisan serve
```
