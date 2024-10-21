FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install \
    intl \
    pdo_mysql \
    zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN composer install

RUN chown -R www-data:www-data var

# Add site configuration for Apache
COPY deploy/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/public/g' /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
