FROM php:7.2.13-fpm-alpine3.8

# Configure PHP

RUN apk update && apk add build-base \
  wget \
  curl \
  git \
  grep \
  zlib-dev \
  zip \
  freetype-dev \
  libjpeg-turbo-dev \
  libpng-dev

RUN docker-php-ext-install mysqli mbstring pdo pdo_mysql tokenizer
RUN docker-php-ext-configure gd --with-gd --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

RUN adduser -h /home/apiuser -s /bin/sh -D apiuser apiuser

USER root
WORKDIR /apidist
COPY --chown=apiuser:apiuser . /apidist
RUN chown apiuser:apiuser /apidist

USER apiuser
RUN mkdir -p /apidist/storage/app/images/tmp
RUN mkdir -p /apidist/storage/app/public
RUN mkdir -p /apidist/storage/logs
RUN mkdir -p /apidist/storage/framework/cache
RUN mkdir -p /apidist/storage/framework/sessions
RUN mkdir -p /apidist/storage/framework/testing
RUN mkdir -p /apidist/storage/framework/views

#RUN composer install --prefer-source --prefer-dist --no-dev --optimize-autoloader --no-interaction
RUN composer install
RUN ln -sf /apidist/storage/app/public /apidist/public/storage