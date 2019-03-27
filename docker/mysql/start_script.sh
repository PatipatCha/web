#!/bin/sh
# MySQL start script

sleep 10;

mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD -e "CREATE DATABASE makro_b2c DEFAULT CHARSET=utf8"
mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD makro_b2c < /ltm_translations.sql
mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD makro_b2c < /migrations.sql
mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD makro_b2c < /password_resets.sql
mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD makro_b2c < /sessions.sql
mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD makro_b2c < /users.sql

mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD -e "update mysql.user set Host = '%'"
mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD -e "FLUSH PRIVILEGES"