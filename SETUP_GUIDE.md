# Lab Platform Setup Guide

This guide describes how to set up the Lab Platform project on a new machine.

## 1. System Requirements

Before starting, ensure your new machine has the following software installed:

*   **OS**: Linux (Ubuntu/Debian recommended) or macOS.
*   **PHP**: Version 8.2 or 8.3.
    *   Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `pdo_mysql`, `xml`, `zip`.
*   **Composer**: Latest version.
*   **Node.js & NPM**: LTS version (v20+ recommended).
*   **Database**: MySQL 8.0+ or MariaDB 10.5+.
*   **Web Server**: Apache 2.4+.

## 2. Installation Steps

### 2.1 Clone the Repository
```bash
git clone <your-repo-url> lab-platform
cd lab-platform
```

### 2.2 Install Backend Dependencies
```bash
composer install
```

### 2.3 Install Frontend Dependencies
```bash
npm install
npm run build
```

## 3. Configuration

### 3.1 Environment Setup
Copy the example environment file:
```bash
cp .env.example .env
```

Open `.env` and configure your database settings:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lab_platform
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Generate the application key:
```bash
php artisan key:generate
```

### 3.2 Database Setup
Create the database (if it doesn't exist) and run migrations:
```bash
php artisan migrate --seed
```
*Note: The `--seed` flag populates the database with initial required data.*

## 4. Web Server Configuration (Apache)

1.  **Enable Modules**:
    ```bash
    sudo a2enmod rewrite proxy proxy_fcgi setenvif
    ```

2.  **Create VirtualHost**:
    Create `/etc/apache2/sites-available/lab-platform.conf` with:
    ```apache
    <VirtualHost *:80>
        ServerName lab-platform.local
        ServerAlias *
        DocumentRoot /var/www/lab-platform/public

        <Directory /var/www/lab-platform/public>
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/lab-platform_error.log
        CustomLog ${APACHE_LOG_DIR}/lab-platform_access.log combined

        # PHP Handling (Adjust version if needed)
        <FilesMatch \.php$>
            SetHandler "proxy:unix:/var/run/php/php8.3-fpm.sock|fcgi://localhost/"
        </FilesMatch>
    </VirtualHost>
    ```

3.  **Enable Site & Restart**:
    ```bash
    sudo a2ensite lab-platform
    sudo systemctl restart apache2
    ```

4.  **Permissions**:
    Ensure the web server can write to storage:
    ```bash
    sudo chown -R www-data:www-data storage bootstrap/cache
    sudo chmod -R 775 storage bootstrap/cache
    ```


## 6. Windows Setup (For Friend)

For Windows Users, we recommend using **Laragon** as it provides a complete, portable development environment (Apache, PHP, MySQL) that matches our stack.

### 6.1 Prerequisites
1.  **Install Laragon**: Download "Laragon Full" from [laragon.org](https://laragon.org/download/).
2.  **Install Node.js**: Download LTS from [nodejs.org](https://nodejs.org/).
3.  **Install Git**: Download from [git-scm.com](https://git-scm.com/).

### 6.2 Installation Steps
1.  **Clone Project**:
    Open Laragon's Terminal (Cmder) and navigate to the www folder:
    ```bash
    cd C:\laragon\www
    git clone <your-repo-url> lab-platform
    cd lab-platform
    ```

2.  **Install Dependencies**:
    ```bash
    composer install
    npm install
    npm run build
    ```

3.  **Configure Environment**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Edit `.env` DB credentials (default Laragon User: `root`, Password: empty).*

4.  **Database**:
    Start Laragon ("Start All" button). It will auto-detect the project and create a hostname `lab-platform.test` (if configured) or you can access via `localhost`.
    ```bash
    php artisan migrate --seed
    ```

### 6.3 Apache Config (Windows)
Laragon typically auto-configures VirtualHosts. If you need manual config, it is found in `C:\laragon\etc\apache2\sites-enabled\auto.lab-platform.test.conf`.
Ensure the `DocumentRoot` points to `C:/laragon/www/lab-platform/public`.

## 7. Verification
Visit `http://localhost` (or your configured domain) to verify the application is running.

