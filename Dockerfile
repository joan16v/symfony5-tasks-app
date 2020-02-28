FROM php:7.3-apache
#Install git
RUN apt-get update \
    && apt-get install -y git
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite
#Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=. --filename=composer
RUN mv composer /usr/local/bin/
RUN rm -rf /var/www/html/var/cache/prod \
    && rm -rf /var/www/html/var/cache/dev
RUN chown -R www-data /var/www
RUN chmod -R 777 /var/www
RUN php bin/console cache:clear
RUN chmod 777 -R var/log
COPY ./ /var/www/html/
EXPOSE 80
