FROM alpine:edge

RUN echo ">>> Install tools" && \
    apk add --no-cache wget bash ca-certificates && \
    echo ">>> Install php packages" && \
    apk add --no-cache \
        php7 php7-json php7-phar php7-iconv php7-openssl php7-zlib \
        php7-mbstring php7-json php7-ctype php7-xml php7-xmlwriter php7-simplexml php7-dom \
        php7-pecl-xdebug php7-tokenizer php7-curl && \
    echo "zend_extension=xdebug.so" > /etc/php7/conf.d/xdebug.ini && \
    echo ">>> Install composer" && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    composer global require "hirak/prestissimo:^0.3.8"

WORKDIR /app
COPY . .

RUN echo ">>> Update sources" && \
#    composer update && \
    echo ">>> Test" && \
    composer infect
#    composer check
