# Smart Budget Manager

A Laravel-based application to manage your personal finances, track budgets, and handle transactions efficiently.

---

## Table of Contents
- [Prerequisites](#prerequisites)
- [Installation Steps](#installation-steps)
  - [Clone the Repository](#clone-the-repository)
  - [Install PHP Dependencies](#install-php-dependencies)
  - [Install Frontend Dependencies](#install-frontend-dependencies)
  - [Set Up Environment File](#set-up-environment-file)
  - [Generate Application Key](#generate-application-key)
  - [Set Up the Database](#set-up-the-database)
  - [Run the Application](#run-the-application)
- [Troubleshooting](#troubleshooting)
- [Additional Notes](#additional-notes)
- [Contributing](#contributing)
- [License](#license)

---

## Prerequisites

Before setting up the Smart Budget Manager on your device, ensure you have the following installed:

- **PHP** (version 8.1 or higher)
- **Composer** (dependency manager for PHP)
- **Node.js** and **npm** (for frontend assets)
- **MySQL** (or another supported database like PostgreSQL)
- **Git** (to clone the repository)
- A web server like **Apache** or **Nginx** (or use Laravel's built-in server for development)

---

## Installation Steps

Follow these steps to set up and run the Smart Budget Manager on your device:

### 1. Clone the Repository

Open a terminal and clone the repository to your local machine:

```bash
git clone https://github.com/HassanAbdelhamed22/Smart-Budget-Manager.git
cd Smart-Budget-Manager


Install PHP Dependencies
Use Composer to install the required PHP packages:
composer install

If you encounter any issues, ensure your PHP version matches the requirements in composer.json.

Install Frontend Dependencies
Install the JavaScript and CSS dependencies using npm:
npm install
npm run build

This will compile the frontend assets using Laravel Vite (or Laravel Mix, depending on the project setup).

Set Up Environment File
Copy the example environment file and configure it:
cp .env.example .env

Open the .env file in a text editor and update the following settings:

Database Configuration: Set your database credentials (e.g., MySQL):DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_budget_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password


App URL: If you're using a custom domain or port, update:APP_URL=http://localhost




Generate Application Key
Generate a unique application key for Laravel:
php artisan key:generate


Set Up the Database
Create a database for the app (e.g., using MySQL):
mysql -u your_username -p
CREATE DATABASE smart_budget_manager;
EXIT;

Run the migrations to set up the database tables:
php artisan migrate

Optionally, seed the database with sample data if the project includes seeders:
php artisan db:seed


Run the Application
Start the Laravel development server:
php artisan serve

By default, the app will be available at http://localhost:8000. Open this URL in your browser to access the Smart Budget Manager.


Troubleshooting

Composer Issues: If composer install fails, ensure your PHP version matches the requirements in composer.json. You can check your PHP version with php -v.
Database Connection Errors: Double-check your .env file for correct database credentials. Ensure your database server (e.g., MySQL) is running.
Frontend Assets Not Loading: If the UI looks broken, ensure you ran npm install and npm run build successfully.
Permission Issues: If you encounter file permission errors, adjust the permissions for the storage and bootstrap/cache directories:chmod -R 775 storage bootstrap/cache



Additional Notes

Authentication: If the app requires user authentication, you may need to register a new user through the app’s UI or use a default user provided by a seeder.
Production Setup: For a production environment, configure a proper web server (e.g., Apache or Nginx) and set APP_ENV=production in your .env file. Also, run:php artisan config:cache
php artisan route:cache


Mail Configuration: If the app sends emails (e.g., for password resets), configure the mail settings in the .env file (e.g., using Mailtrap for testing).

Contributing
If you’d like to contribute to the Smart Budget Manager, feel free to fork the repository, make your changes, and submit a pull request.
License
This project is open-source and licensed under the MIT License. See the LICENSE file for more details.
