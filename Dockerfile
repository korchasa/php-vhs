
FROM alpine:3.10

RUN echo ">>> Install tools" && \
    apk add --no-cache wget bash ca-certificates && \
    echo ">>> Install php packages" && \
    apk add --no-cache \
        php7 php7-json php7-phar php7-iconv php7-openssl php7-zlib \
        php7-mbstring php7-json php7-ctype php7-xml php7-xmlwriter php7-simplexml php7-dom \
        php7-pecl-xdebug php7-tokenizer php7-curl && \
    echo "zend_extension=xdebug.so" > /etc/php7/conf.d/xdebug.ini && \
    echo ">>> Install composer" && \
    wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet --install-dir /bin --filename composer && \
    composer global require "hirak/prestissimo:^0.3.8"

WORKDIR /app
COPY . .

RUN composer check
