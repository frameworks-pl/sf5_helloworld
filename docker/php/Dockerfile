FROM php:7.4.1-apache
RUN apt-get update
RUN apt-get install -qq -y vim git unzip iputils-ping libpq-dev sudo net-tools sendmail
RUN pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug
RUN docker-php-ext-install sockets
RUN mv /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini_org
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf