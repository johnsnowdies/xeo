FROM dockette/php:7.1-fpm

RUN apt-get update \
    && apt-get -y --no-install-recommends install  php7.1-mysql php7.1-gmp php7.1-cgi php7.1-cli php7.1-curl php7.1-json php7.1-odbc php7.1-tidy php7.1-common php7.1-xmlrpc php7.1-gd php-pear php7.1-dev php7.1-imap php7.1-mcrypt php7.1-mysqlnd php7.1-mbstring php7.1-curl php7.1-dom php7.1-zip php7.1-soap php-xdebug git mysql-client php-xdebug \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && echo "xdebug.idekey=PHPSTORM" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
    && echo "xdebug.default_enable=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_autostart=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_connect_back=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
    && echo "xdebug.profiler_enable=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_host=172.17.0.1" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
    && echo "short_open_tag" = On >> /etc/php/7.1/fpm/php.ini

WORKDIR "/app"
