FROM php:8.2-alpine3.18

# Setting working directory to run command inside project
WORKDIR /var/www

# Copy Project
COPY .env .
COPY . .

# Installing Dependencies of container and application
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli.so \
# Installing Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction \
    # && php artisan migrate
    && chmod +x ./cmd.sh

# Expose port 8000 and start php-fpm server
EXPOSE 8000 

CMD ["/bin/sh", "-c", "./cmd.sh"] 
