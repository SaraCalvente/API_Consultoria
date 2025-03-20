#Base image: .docker/common/Dockerfile
FROM base-image

# copy config php.ini
COPY .docker/local/php/php.ini /usr/local/etc/php/conf.d/zz-docker.ini

# Setup PHP-FPM config settings (Calculator https://spot13.com/pmcalculator/)
RUN echo "pm.max_children = 54" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
     echo "pm.start_servers = 13" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
     echo "pm.min_spare_servers = 13" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
     echo "pm.max_spare_servers = 40" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
     echo "pm.max_requests = 500" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# Execute setup
COPY .docker/local/setup.sh /var/www/.docker/local/setup.sh
RUN chmod +x /var/www/.docker/local/setup.sh
CMD ./.docker/local/setup.sh

EXPOSE 9000