FROM php:8.3-apache as dev

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN addgroup --gid 1000 penny && adduser --disabled-password --gecos "" --ingroup penny --uid 1000 penny


COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt update && apt install -y libzip-dev zip unzip 7zip

RUN docker-php-ext-install pdo_mysql zip

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

USER penny

FROM php:8.3-apache as prod

RUN apt update && apt install curl zip unzip libzip-dev -y

RUN docker-php-ext-install pdo_mysql exif zip bcmath

WORKDIR /var/www/html/

COPY ./ /var/www/html

RUN rm -rf tests/

RUN chown -R www-data:www-data /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

