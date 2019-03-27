FROM eggdevop/docker-apache-php:php7

RUN apt-get install php7.0-soap

# Disable default site so that all connections on PORT 80 go to vhost.conf
RUN a2dissite 000-default

# Set LOG_PATH
RUN mkdir /data
RUN mkdir /data/log
RUN mkdir /data/log/web

RUN chmod -R 777 /data

ADD sites-enabled/vhost.conf /etc/apache2/sites-enabled/