#!/usr/bin/env bash

sh .docker/common/setup/add-folder-permissions.sh

# install dependencies
composer install

/usr/bin/supervisord