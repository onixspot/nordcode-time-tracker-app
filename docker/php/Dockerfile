FROM php:7.4-fpm

ARG uid
ARG appDir=/app
ARG timezone=UTC

RUN ln -snf "/usr/share/zoneinfo/${timezone}" /etc/localtime \
 && echo "${timezone}" > /etc/timezone

RUN apt-get -qq update \
 && apt-get -qq upgrade -y \
 && apt-get -qq install -y --no-install-recommends \
        acl \
        curl \
        git \
        openssl \
        unzip \
        wget \
        zip

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN chmod +x /usr/bin/install-php-extensions \
  && sync \
  && install-php-extensions \
        apcu \
        bz2 \
        gd \
        intl \
        mysqli \
        opcache \
        pdo_mysql \
        xdebug \
        zip

RUN mv ${PHP_INI_DIR}/php.ini-development ${PHP_INI_DIR}/php.ini \
 && sed -i 's!^;cgi.fix_pathinfo=.*!cgi.fix_pathinfo = 0!g' ${PHP_INI_DIR}/php.ini \
 && sed -i 's!^;error_log.*!error_log = \/dev\/stdout!g' ${PHP_INI_DIR}/php.ini \
 && sed -i 's!^display_errors.*!display_errors = On!g' ${PHP_INI_DIR}/php.ini \
 && sed -i 's!^error_reporting.*!error_reporting = E_ALL!g' ${PHP_INI_DIR}/php.ini \
 && sed -i 's!^display_startup_errors.*!display_startup_errors = On!g' ${PHP_INI_DIR}/php.ini \
 && sed -i "s!^;date.timezone.*!date.timezone = ${timezone}!g" ${PHP_INI_DIR}/php.ini

RUN mkdir -p ${appDir} \
 && usermod --non-unique --uid ${uid} www-data \
 && groupmod --non-unique --gid ${uid} www-data \
 && chown -R www-data:www-data /var/www ${appDir}

RUN ( curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer ) \
 && ( curl -sS https://get.symfony.com/cli/installer | bash ) \
 && mv ~/.symfony/bin/symfony /usr/bin/symfony

USER www-data
WORKDIR ${appDir}
EXPOSE 9000