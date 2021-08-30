FROM phpdockerio/php74-fpm:latest


RUN apt update && apt install -y mc \
    apt-transport-https \
    git \
    software-properties-common \
    php7.4 \
    php7.4-bcmath \
    php7.4-curl \
    php7.4-gd \
    php7.4-imap \
    php7.4-intl \
    php7.4-mbstring \
    php7.4-pgsql \
    php7.4-soap \
    php7.4-xmlrpc \
    php7.4-xsl \
    php7.4-zip \
    php-imagick \
    php-redis \
    php-xdebug \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


WORKDIR /var/www/api_test_gazprom

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN echo "alias sf='symfony console'" > ~/.bash_aliases
RUN echo "PS1='${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '" > ~/.bashrc

RUN git config --global user.email "USER@DOMAIN.COM" && git config --global user.name "USERNAME"

