#!/bin/bash
docker-compose pull
docker-compose build
docker-compose -f docker-compose.yml up -d
sleep 5;

docker exec xd42_app_1 composer global require "fxp/composer-asset-plugin:^1.2.0"
docker exec xd42_app_1 composer install -d /app
docker exec -i xd42_db_1 mysql -u root --password=root db < data/dump.sql
