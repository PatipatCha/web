#!/bin/sh
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
echo $DIR

docker rm -f makro_web_b2c
docker rm -f makro_web_mysql
docker rm -f makro_web_pma
docker-compose rm

docker-compose build makro_web_b2c
docker-compose up -d makro_web_b2c

docker-compose build makro_web_mysql
docker-compose up -d makro_web_mysql

docker-compose up -d makro_web_pma

sleep 5
docker exec -it makro_web_mysql sh /start_script.sh

sleep 3
docker exec -it makro_web_b2c sh /start_script.sh

sleep 5
docker exec -it makro_web_b2c sh /etc/init.d/apache2 start

# Run composer install only when ../vendor does not exist
if [ ! -d ../vendor ]; then
  docker exec -it makro_web_b2c composer install
fi

# Laravel
chmod -R 777 $DIR/../storage
chmod -R 777 $DIR/../public/image/barcode
chmod -R 777 $DIR/../vendor
chmod -R 777 $DIR/../resource