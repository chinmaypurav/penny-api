FROM php:8.3.2-apache

ADD ./www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup --gid 1000 penny && adduser --disabled-password --gecos "" --ingroup penny --uid 1000 penny

RUN mkdir -p /var/www/html

WORKDIR /var/www/html/

RUN apt update -y

RUN apt install git curl zip unzip libzip-dev sqlite3 -y

RUN docker-php-ext-install pdo pdo_mysql exif zip bcmath

RUN chown penny:penny /var/www/html

USER penny

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/start

CMD ["/usr/local/bin/start"]
