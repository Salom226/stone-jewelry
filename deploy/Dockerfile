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

# Add site configuration for Apache
COPY deploy/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/public/g' /etc/apache2/apache2.conf

# Copy the postinstall.sh script
COPY deploy/postinstall.sh /usr/local/bin/postinstall.sh
RUN chmod +x /usr/local/bin/postinstall.sh

EXPOSE 80

# Run the postinstall.sh script and then start Apache
CMD ["/bin/bash", "-c", "/usr/local/bin/postinstall.sh && apache2-foreground"]
