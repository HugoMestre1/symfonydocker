FROM ubuntu

MAINTAINER Henrique Sobral<henrique.sobral@zap.co.ao>

# Install packages
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
  apt-get -y install supervisor git apache2 mysql-client pwgen && \
  echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Requirements for php packages
RUN apt-get -y install software-properties-common && \
	add-apt-repository -y ppa:ondrej/php

RUN apt-get update && \
    apt-get -y --allow-unauthenticated install \
    libapache2-mod-php7.1 \
    automake \
    autoconf \
    nasm \
    build-essential \
    libpng-dev \
    php-xdebug \
    iputils-ping \
    bash \
    curl \
    nano \
    libtool \
    ca-certificates \
    php7.1-apc \
    php7.1-cli \
    php7.1-ctype \
    php7.1-curl \
    php7.1-dom \
    php7.1-fileinfo \
    php7.1-ftp \
    php7.1-iconv \
    php7.1-json \
    php7.1-mbstring \
    php7.1-mongodb \
    php7.1-mysqlnd \
    php7.1-mysql \
    php7.1-pdo \
    php7.1-phar \
    php7.1-posix \
    php7.1-simplexml \
    php7.1-sqlite3 \
    php7.1-soap \
    php7.1-tokenizer \
    php7.1-xml \
    php7.1-xmlreader \
    php7.1-xmlwriter \
    php7.1-xdebug \
    php7.1-zip \
    php7.1-gd \
    libxml2 \
    libxml2-dev \
    apache2-utils \
    nano

#Install composer 
RUN curl -sS https://getcomposer.org/installer | php7.1 -- --install-dir=/usr/bin --filename=composer
RUN rm -rf /var/cache/apk/*

# AllowOverride ALL
RUN touch /etc/apache2/httpd.conf
RUN sed -i '264s#AllowOverride None#AllowOverride All#' /etc/apache2/httpd.conf

#Rewrite Moduble Enable
RUN sed -i 's#\#LoadModule rewrite_module modules/mod_rewrite.so#LoadModule rewrite_module modules/mod_rewrite.so#' /etc/apache2/httpd.conf

#PHP configurations
RUN sed -e 's/max_execution_time = 30/max_execution_time = 10000/' -i /etc/php/7.1/cli/php.ini

# Document Root to /var/www/html/
RUN sed -i 's#/var/www/localhost/htdocs#/var/www/html#g' /etc/apache2/httpd.conf

# COPY FILE 000-default.conf
COPY 000-default.conf 	/etc/apache2/sites-available/

# Create apache env
RUN mkdir -p /run/apache2

# USER apache
WORKDIR /var/www/html/

#Activate rewrite 
RUN a2enmod rewrite

EXPOSE 80
EXPOSE 443

CMD ["apache2ctl", "-D", "FOREGROUND"]