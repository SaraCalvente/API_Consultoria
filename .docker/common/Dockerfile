#Pull php image, PHP 8.2 fast cgi
FROM php:8.2.3-fpm-bullseye

#Establish base working dir
WORKDIR /var/www/

# Set Timezone
ENV TZ="Europe/Madrid"
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#Install composer
COPY --from=composer:2.5.1 /usr/bin/composer /usr/bin/composer

#Install all necesary packages and extensions
RUN apt-get update && apt-get install -y \
         git \
         unzip \
         zlib1g-dev \
         libxml2-dev \
         libzip-dev \
         libpng-dev \
         libjpeg-dev \
         cron \
         supervisor \
     && docker-php-ext-configure gd --enable-gd --with-jpeg \
     && docker-php-ext-install \
         zip \
         intl \
 		 mysqli \
         pdo pdo_mysql \
         gd

# Install Nginx
RUN apt-get install -y nginx && \
    rm /etc/nginx/sites-enabled/default && \
    apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
COPY .docker/common/nginx/sites-enabled/* /etc/nginx/sites-enabled/

# Supervisor config
COPY .docker/common/supervisord/process/* /etc/supervisor/conf.d/
COPY .docker/common/supervisord/supervisord.conf /etc/supervisor/

# Config crontab
COPY .docker/common/cron/project-crontab /etc/cron.d/project-crontab
RUN chmod 0644 /etc/cron.d/project-crontab && crontab /etc/cron.d/project-crontab