FROM php:fpm-alpine

ENV NODE_VERSION 20.4.0
ENV YARN_VERSION 1.22.21


WORKDIR /var/www/arkscan

ADD docker/entrypoint.sh /entrypoint.sh
ADD docker/nginx-default.conf /etc/nginx/http.d/default.conf
ADD docker/supervisord.conf /etc/supervisord.conf

RUN adduser -S -h /var/www/arkscan -s /bin/bash -G www-data -u 1000 arkscan arkscan \
    && apk add --no-cache --virtual .build-deps curl gnupg tar autoconf dpkg-dev dpkg file pkgconf re2c pcre-dev \
    && apk add --no-cache sqlite-dev postgresql-dev libzip-dev libsodium-dev gmp-dev dcron bash sudo libstdc++ nginx redis supervisor make gcc g++ python3  \
    && sed -i -e "s/bin\/ash/bin\/bash/" /etc/passwd \
    && echo 'arkscan ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers \
    && docker-php-ext-configure bcmath --enable-bcmath \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure pdo_pgsql --with-pdo-pgsql \
    && docker-php-ext-configure exif \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gmp \
    && docker-php-ext-configure pdo_sqlite \
    && docker-php-ext-configure intl \
    && docker-php-ext-install bcmath pcntl pdo_pgsql exif zip gmp pdo_sqlite intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit = 1024M" >> $PHP_INI_DIR/php.ini \
    && curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -fsSLO --compressed "https://unofficial-builds.nodejs.org/download/release/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64-musl.tar.xz" \
    && tar -xJf "node-v$NODE_VERSION-linux-x64-musl.tar.xz" -C /usr/local --strip-components=1 --no-same-owner \
    && ln -s /usr/bin/node /usr/bin/nodejs \
    && curl -fsSLO --compressed "https://yarnpkg.com/downloads/$YARN_VERSION/yarn-v$YARN_VERSION.tar.gz" \
    && mkdir -p /opt \
    && tar -xzf yarn-v$YARN_VERSION.tar.gz -C /opt/ \
    && ln -s /opt/yarn-v$YARN_VERSION/bin/yarn /usr/local/bin/yarn \
    && ln -s /opt/yarn-v$YARN_VERSION/bin/yarnpkg /usr/local/bin/yarnpkg \
    && rm -f yarn-v$YARN_VERSION.tar.gz.asc yarn-v$YARN_VERSION.tar.gz \
    && rm -f node-v$NODE_VERSION-linux-x64-musl.tar.xz \
    && echo '* * * * * php artisan schedule:run >> /dev/null 2>&1' > /etc/crontabs/arkscan \
    && apk del .build-deps \ 
    && rm -rf /tmp/*
USER arkscan 
ENTRYPOINT ["bash", "-c", "/entrypoint.sh \"$@\"", "--"]
