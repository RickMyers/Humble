FROM ubuntu:22.04

RUN apt update && apt upgrade -y 

ENV TZ=America/Los_Angeles

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt -y install software-properties-common
RUN update-ca-certificates
RUN apt update
RUN apt install wget -y
RUN apt install curl -y
#RUN curl 'https://keyserver.ubuntu.com/pks/lookup?op=get&options=mr&exact=on&search=0x14AA40EC0831756756D7F66C4F4EA0AAE5267A6C' | sudo apt-key add -
RUN apt install apache2 -y
RUN apt update
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN apt install -y curl
RUN apt install -y gnupg2
RUN apt install -y php libapache2-mod-php php-mysql
RUN apt install -y cron
RUN apt install -y php-mongodb
RUN apt install -y nodejs
RUN apt install -y npm
RUN apt install -y php-mbstring
RUN apt install -y php-xml
RUN apt install -y php-yaml
RUN apt install -y php-json
RUN apt install -y nano
RUN apt install -y git
RUN apt install -y vim
RUN apt install -y php-zip
RUN apt install -y memcached
RUN apt install -y libmemcached-tools
RUN apt install -y php-memcached 
RUN apt install -y php-memcache
RUN apt install -y php-xdebug
RUN apt install -y mysql-client
RUN apt install -y libapache2-mod-dnssd
RUN apt install -y php-gd
RUN apt install -y htop
RUN apt install -y php-dev
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/bin
RUN git config --global http.sslverify false
RUN service memcached start
RUN mkdir /var/www/logs && chmod -R 0777 /var/www/logs
RUN mkdir /var/www/logs/argus && chmod -R 0777 /var/www/logs/argus
RUN a2enmod rewrite && a2enmod ssl && a2enmod include
COPY vhost.conf /etc/apache2/sites-enabled/humble.com.conf
RUN apt upgrade -y
CMD ["apache2ctl", "-D", "FOREGROUND"]
