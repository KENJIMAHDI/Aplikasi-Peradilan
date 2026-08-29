FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html

ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Install dependencies & build frontend assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

EXPOSE 80
