FROM php:8.1.2
RUN apt-get update && apt-get install -y \
        sendmail \
        libjpeg62-turbo-dev \
        libpng-dev \
        vim \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-configure pdo \
    && docker-php-ext-install pdo \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install pdo_mysql

ENV APP_DIR /app
ENV APPLICATION_ENV prod

WORKDIR $APP_DIR

COPY api/ /app
COPY .env /app
COPY ./config/php.ini /usr/local/etc/php

EXPOSE 80

RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/php-sendmail.ini

# Fully qualified domain name configuration for sendmail on localhost.
# Without this sendmail will not work.
# This must match the value for 'hostname' field that you set in ssmtp.conf.
RUN sed -i '/#!\/bin\/sh/aservice sendmail restart' /usr/local/bin/docker-php-entrypoint
RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint

CMD ["php", "-S", "0.0.0.0:80", "server.php"]
