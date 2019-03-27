#!/bin/sh
docker rm -f makro_web_b2c

docker-compose rm

docker-compose build makro_web_b2c
docker-compose up -d makro_web_b2c


sleep 5
docker exec -it makro_web_b2c sh /etc/init.d/apache2 start

sleep 3
sh web/start_script.sh


