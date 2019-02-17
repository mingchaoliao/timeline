FROM php:7.2.13-fpm-alpine3.8

RUN apk update && apk add build-base \
  wget \
  curl \
  git \
  grep \
  zlib-dev \
  zip \
  mysql-client \
  freetype-dev \
  libjpeg-turbo-dev \
  libpng-dev

RUN docker-php-ext-install mysqli mbstring pdo pdo_mysql tokenizer pcntl zip
RUN docker-php-ext-configure gd --with-gd --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

RUN adduser -h /home/tlmgr -s /bin/sh -u 1500 -g 1500 -D tlmgr tlmgr

RUN echo '* * * * * cd /apidist && php artisan schedule:run >> /dev/null 2>&1' | crontab -u tlmgr -

USER root
WORKDIR /apidist
COPY --chown=tlmgr:tlmgr . /apidist
RUN chown tlmgr:tlmgr /apidist

USER tlmgr
RUN mkdir -p /apidist/storage/app/images
RUN mkdir -p /apidist/storage/app/public
RUN mkdir -p /apidist/storage/logs
RUN mkdir -p /apidist/storage/framework/cache
RUN mkdir -p /apidist/storage/framework/sessions
RUN mkdir -p /apidist/storage/framework/testing
RUN mkdir -p /apidist/storage/framework/views

RUN composer install --prefer-source --prefer-dist --no-dev --optimize-autoloader --no-interaction
RUN php artisan key:generate && php artisan jwt:secret