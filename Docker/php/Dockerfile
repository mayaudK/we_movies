FROM php:8.3.10-fpm-bullseye
MAINTAINER "Kévin MAYAUD <kevin.mayaud@hotmail.fr>"


RUN apt update \
    &&  apt upgrade -y \
    &&  apt install -y --no-install-recommends \
        locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip libpq-dev wget \
        apt-transport-https lsb-release ca-certificates \
    && apt install -y vim make curl gnupg2

# Download and configure CA certificates
RUN mkdir -p /etc/ssl/certs \
    && curl -o /etc/ssl/certs/cacert.pem https://curl.se/ca/cacert.pem

RUN echo "curl.cainfo = /etc/ssl/certs/cacert.pem" > /usr/local/etc/php/conf.d/curl.ini \
    && echo "openssl.cafile = /etc/ssl/certs/cacert.pem" >> /usr/local/etc/php/conf.d/openssl.ini

# Install Node.js 22.x
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs \
    &&  npm install -g yarn

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen  \
    &&  echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
    &&  locale-gen

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    &&  mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN docker-php-ext-install pdo pdo_pgsql opcache

RUN pecl install apcu && docker-php-ext-enable apcu opcache

RUN yarn install

RUN apt clean \
    &&  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    &&  rm -rf /usr/share/doc /usr/share

## Add permissions to ubuntu user
#ARG USER_ID=1000
#ARG GROUP_ID=1000
#
#RUN usermod -u $USER_ID www-data \
#    &&  groupmod -g $GROUP_ID www-data \
#    &&  chown -R www-data:www-data /var/www/html

# Change to /app
WORKDIR /var/www/html/

# Copy your source file directory into /app
COPY . /var/www/html/

CMD tail -f /dev/null