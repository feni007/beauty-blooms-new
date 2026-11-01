FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -s 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -s 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf