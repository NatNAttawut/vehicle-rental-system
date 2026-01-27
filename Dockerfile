FROM php:8.2-apache

# เปิด mod_rewrite (เผื่อใช้ .htaccess)
RUN a2enmod rewrite

# ติดตั้ง extension ที่มักต้องใช้ (curl สำคัญกับ supabase)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
 && docker-php-ext-install zip \
 && docker-php-ext-install mysqli \
 && docker-php-ext-install pdo pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

# ตั้งค่า Apache ให้อนุญาต .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# คัดลอกโค้ดไปยัง web root
COPY . /var/www/html/

# (แนะนำ) ให้ Render ใช้ Port 80 ตาม apache
EXPOSE 80
