#!/bin/bash

echo -e ${WARN_COLOR}
echo "===> Pulling and spinning up Docker containers"
echo -e ${NO_COLOR}

#cp .env.example .env

docker-compose pull
docker-compose build
docker-compose -f docker-compose.yml up -d
sleep 5;

echo -e ${WARN_COLOR}
echo "===> Installing composer"
echo -e ${NO_COLOR}

#docker exec xch_app_1 composer global require "fxp/composer-asset-plugin:^1.2.0"
#docker exec xch_app_1 composer install -d /app


echo -e ${WARN_COLOR}
echo "===> Dump DB"
echo -e ${NO_COLOR}


#docker exec -i xch_app_1 php /app/yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations --interactive=0
#docker exec -i xch_app_1 php /app/yii migrate --interactive=0
#docker exec -i xch_app_1 php /app/yii migrate --migrationPath=@yii/log/migrations/ --interactive=0
docker exec -i xch_db_1 mysql -u root --password=root db < data/dump.sql

echo -e ${WARN_COLOR}
echo "=== !!! We are done, congrats !!! ==="
echo -e ${NO_COLOR}
