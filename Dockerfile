FROM php:7.3-apache
RUN apt-get update
RUN docker-php-ext-install pdo pdo_mysql mysqli

