FROM php:8.3-apache


RUN mkdir -p /var/www/html



RUN apt update -y

RUN apt install curl zip unzip libzip-dev -y

RUN docker-php-ext-install pdo_mysql exif zip bcmath

WORKDIR /var/www/html/

COPY ./ /var/www/html

RUN chown -R www-data:www-data /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev

COPY start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/start

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite


CMD ["/usr/local/bin/start"]
