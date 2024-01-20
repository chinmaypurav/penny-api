FROM php:8.3.2-apache


RUN mkdir -p /var/www/html



RUN apt update -y

RUN apt install git curl zip unzip libzip-dev sqlite3 -y

RUN docker-php-ext-install pdo pdo_mysql exif zip bcmath

WORKDIR /var/www/html/

COPY ./ /var/www/html

RUN chown -R www-data:www-data /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev

COPY start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/start

CMD ["/usr/local/bin/start"]
