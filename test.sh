##!/bin/bash
#
#rm -rf src/Migrations
#
#mkdir src/Migrations
#
#
## Eliminar la base de datos
#echo "Eliminando la base de datos..."
#php bin/console doctrine:database:drop --force
#
## Crear la base de datos nuevamente
#echo "Creando la base de datos..."
#php bin/console doctrine:database:create
#
##Creacion de migraciones
#echo "Creando migraciones..."
#php bin/console doctrine:migrations:diff
#
## Ejecutar las migraciones para crear las tablas
#echo "Ejecutando migraciones..."
#php bin/console doctrine:migrations:migrate --no-interaction
#
## Cargar las fixtures si las tienes
#echo "Cargando fixtures..."
#php bin/console doctrine:fixtures:load --no-interaction
#
#echo "Base de datos restablecida con éxito."

#!/bin/bash

echo "rebuilding database ..."
php bin/console doctrine:schema:drop -n -q --force --full-database
rm src/Migrations/*.php
php bin/console make:migration
php bin/console doctrine:migrations:migrate -n -q
php bin/console doctrine:fixtures:load -n -q
