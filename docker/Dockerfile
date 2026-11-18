FROM php:fpm-alpine

ENV NODE_VERSION 24.22.2
ENV PNPM_HOME=/var/www/arkscan/.pnpm
ENV PATH="${PATH}:${PNPM_HOME}/bin"


WORKDIR /var/www/arkscan

ADD docker/entrypoint.sh /entrypoint.sh
ADD docker/nginx-default.conf /etc/nginx/http.d/default.conf
ADD docker/supervisord.conf /etc/supervisord.conf

RUN adduser -S -h /var/www/arkscan -s /bin/bash -G www-data -u 1000 arkscan arkscan \
    && apk add --no-cache --virtual .build-deps curl gnupg tar autoconf dpkg-dev dpkg file pkgconf re2c pcre-dev \
    && apk add --no-cache sqlite-dev postgresql-dev libzip-dev libsodium-dev gmp-dev dcron bash sudo libstdc++ nginx redis supervisor make gcc g++ python3 git \
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
    && npm install -g npm@latest \
    && su arkscan -c "npm config set allow-scripts=pnpm --location=user" \
    && su arkscan -c "npm install --prefix=/var/www/arkscan/.pnpm -g pnpm" \
    && rm -f node-v$NODE_VERSION-linux-x64-musl.tar.xz \
    && echo '* * * * * php artisan schedule:run >> /dev/null 2>&1' > /etc/crontabs/arkscan \
    && apk del .build-deps \
    && rm -rf /tmp/*

USER arkscan
ENTRYPOINT ["bash", "-c", "/entrypoint.sh \"$@\"", "--"]
