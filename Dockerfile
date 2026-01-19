FROM php:8.2-apache

RUN a2enmod rewrite

COPY vehicle-rental-system-main/ /var/www/html/
EXPOSE 80