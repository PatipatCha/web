#!/bin/sh
docker exec -it makro_web_b2c bash
cd ..
cd ..
mkdir projects
cd projects
mkdir alpha-makro-b2c
cd alpha-makro-b2c
mkdir storage
mkdir bootstrap
chmod -R 777 /var/projects/alpha-makro-b2c/storage
chmod -R 777 /var/projects/alpha-makro-b2c/bootstrap

