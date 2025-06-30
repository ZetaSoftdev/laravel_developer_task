# ER21 School Management System - Setup Manual

## Table of Contents
1. [System Requirements](#1-system-requirements)
2. [Installation Steps](#2-installation-steps)
3. [Key Features & Components](#3-key-features--components)
4. [Important Configurations](#4-important-configurations)
5. [Post-Installation Steps](#5-post-installation-steps)
6. [Security Considerations](#6-security-considerations)
7. [Maintenance](#7-maintenance)
8. [Troubleshooting](#8-troubleshooting)
9. [Additional Resources](#9-additional-resources)

## 1. System Requirements

### Server Requirements
- PHP >= 8.1
- MySQL/MariaDB
- Web Server (Apache/Nginx)
- Composer (Latest Version)
- Node.js & NPM (for asset compilation)

### Required PHP Extensions
- PDO PHP Extension
- cURL PHP Extension
- ZIP PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- GD PHP Extension

### Server Recommendations
- Memory: Minimum 2GB RAM
- Storage: Minimum 20GB available space
- Processor: 2+ CPU cores
- OS: Linux (Ubuntu/CentOS recommended)

## 2. Installation Steps

### 2.1. Clone the Repository
```bash
git clone <repository-url>
cd ER21
```

### 2.2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies and compile assets
npm install
npm run dev
```

### 2.3. Environment Setup
```bash
# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2.4. Configure Environment File
Edit the `.env` file and set the following configurations:

```env
# Application Settings
APP_NAME=ER21
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database Settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Mail Settings
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Payment Gateway Settings
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret

# File Upload Settings
FILESYSTEM_DISK=local
```

### 2.5. Database Setup
```bash
# Run database migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed --class=AddSuperAdminSeeder
php artisan db:seed --class=DatabaseSeeder
```

### 2.6. Storage Setup
```bash
# Create storage symlink
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 3. Key Features & Components

### 3.1. Core Modules
- User Management System
  - Staff Management
  - Student Management
  - Parent/Guardian Management
  - Role-based Access Control

- Academic Management
  - Class Management
  - Section Management
  - Subject Management
  - Timetable Management
  - Assignment Management
  - Online Examination System

- Attendance System
  - Student Attendance
  - Staff Attendance
  - Reports Generation

- Financial Management
  - Fee Management
  - Payment Processing
  - Expense Tracking
  - Financial Reports

### 3.2. Additional Features
- Notification System
  - Email Notifications
  - In-app Notifications
  - SMS Integration (Optional)

- Document Management
  - File Upload System
  - Document Templates
  - Certificate Generation

- Reporting System
  - Academic Reports
  - Attendance Reports
  - Financial Reports
  - Custom Report Generation

## 4. Important Configurations

### 4.1. Web Server Configuration

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/ER21/public
    
    <Directory /path/to/ER21/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/ER21/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4.2. Cron Jobs Setup
Add the following to your crontab:
```bash
* * * * * cd /path/to/ER21 && php artisan schedule:run >> /dev/null 2>&1
```

### 4.3. Queue Configuration
```env
QUEUE_CONNECTION=database
```

```bash
# Start queue worker
php artisan queue:work --daemon
```

## 5. Post-Installation Steps

### 5.1. Security Measures
1. Update all server software
```bash
sudo apt update && sudo apt upgrade -y
```

2. Configure SSL certificate
```bash
# Using Let's Encrypt
sudo certbot --apache -d your-domain.com
```

3. Set up firewall rules
```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

### 5.2. Performance Optimization
1. Enable PHP OPcache
2. Configure Redis/Memcached for caching
3. Enable Gzip compression
4. Configure browser caching

### 5.3. Backup Configuration
1. Set up automated backups
```bash
# Database backup
mysqldump -u user -p database_name > backup.sql

# File backup
tar -czf backup.tar.gz /path/to/ER21
```

## 6. Security Considerations

### 6.1. File Permissions
```bash
# Set proper ownership
chown -R www-data:www-data /path/to/ER21

# Set proper permissions
find /path/to/ER21 -type f -exec chmod 644 {} \;
find /path/to/ER21 -type d -exec chmod 755 {} \;
```

### 6.2. Security Headers
Add to your `.htaccess` or web server config:
```apache
Header set X-XSS-Protection "1; mode=block"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-Content-Type-Options "nosniff"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

### 6.3. Regular Security Tasks
- Keep all dependencies updated
- Monitor error logs
- Perform regular security audits
- Maintain backup strategy
- Monitor file integrity

## 7. Maintenance

### 7.1. Regular Maintenance Tasks
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear compiled classes
php artisan clear-compiled

# Update dependencies
composer update

# Check for security vulnerabilities
composer audit
```

### 7.2. Database Maintenance
```bash
# Optimize database
php artisan db:optimize

# Run database maintenance
ANALYZE TABLE your_tables;
OPTIMIZE TABLE your_tables;
```

## 8. Troubleshooting

### 8.1. Common Issues and Solutions

#### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage
chown -R www-data:www-data storage

# Fix bootstrap/cache permissions
chmod -R 775 bootstrap/cache
chown -R www-data:www-data bootstrap/cache
```

#### Database Connection Issues
1. Verify database credentials in `.env`
2. Check database server status
3. Verify database user permissions

#### Mail Configuration Issues
1. Test mail settings:
```bash
php artisan tinker
Mail::raw('Test mail', function($message) { $message->to('test@example.com')->subject('Test'); });
```

#### Payment Gateway Issues
1. Verify API credentials
2. Check payment gateway logs
3. Test in sandbox mode first

### 8.2. Debug Mode
Enable debug mode temporarily in `.env`:
```env
APP_DEBUG=true
```

## 9. Additional Resources

### 9.1. Documentation Links
- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Apache Documentation](https://httpd.apache.org/docs/)

### 9.2. Support Channels
- GitHub Issues
- Community Forums
- Technical Support Email

### 9.3. Version Control
- Keep track of changes in version control
- Follow semantic versioning
- Maintain a changelog

---

**Note**: This manual is maintained and updated regularly. For the latest version, please check the repository. 