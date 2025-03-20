#!/usr/bin/env bash

mkdir -p /var/www/var/cache
chmod -R 777 /var/www/var/cache
mkdir -p /var/www/var/log
chmod -R 777 /var/www/var/log

touch /var/www/var/log/cron.log
chmod 777 /var/www/var/log/cron.log