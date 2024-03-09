# Stage 1
# Run composer
FROM  composer:2.7.1 as composer
WORKDIR /app
COPY ./composer.json /app
COPY ./composer.lock /app

# Copy the current directory contents into the container deployment folder
COPY ./src /app/lib
RUN composer install --no-interaction --no-dev --ignore-platform-reqs --optimize-autoloader

# Tidy up
# remove non-required vendor files and anything else we do not want in the archive and do not need for
# the next state
RUN rm /app/composer.*

# Stage 2
# Build the IR container
# Extend from alpine parent image
FROM alpine:3.18.6

MAINTAINER Sample Sync App <Daniel.Ian.Kelley@gmail.com>

# Added meta-data about this app
ARG APP_VERSION="1.0.4"
LABEL vendor=REVOLVE \
      com.github.aguilita1.is-beta="false" \
      com.github.aguilita1.is-production="true" \
      com.github.aguilita1.version=$APP_VERSION \
      com.github.aguilita1.release-date="2024-03-05"

# Install apache, PHP, and supplimentary programs.
RUN apk update && apk upgrade && apk add \
    libcrypto1.1 \
    libssl1.1 \
    tar \
    bash \
    curl \
    php82 \
    php82-curl \
    php82-session \
    php82-fileinfo \
    php82-xml \
    php82-simplexml \
    tzdata \
    && rm -rf /var/cache/apk/*

# Add all necessary config files in one layer
ADD docker/ /

# Update the PHP.ini file
RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php82/php.ini && \
    sed -i "s/session.gc_probability = .*$/session.gc_probability = 50/" /etc/php82/php.ini && \
    sed -i "s/session.gc_divisor = .*$/session.gc_divisor = 100/" /etc/php82/php.ini

# Setup persistent environment variables
ENV SA_PHP_SESSION_GC_MAXLIFETIME=1440 \
    SA_PHP_MAX_EXECUTION_TIME=300 \
    SA_PHP_MEMORY_LIMIT=256M \
    SA_TIME_ZONE=America/New_York \
    SA_SYNC_INTERVAL=120 \
    SA_START_SYNC=04:00:00\
    SA_STOP_SYNC=23:59:59


# Map the source files into /var/www/cms
RUN mkdir -p /opt/ir
COPY --from=composer /app /opt/ir


# Write App version to settings.php file
RUN sed -i "s/.*app_version = 'APP_VERSION'.*$/\$app_version = '$APP_VERSION';/" /opt/ir/lib/main.php

# Map a volumes to this folder.
# Our CMS files, library, cache and backups will be in here.
RUN chown -R nobody /opt/ir && \
    chmod +x /entrypoint.sh

#Set Time Zone
RUN ln -snf /usr/share/zoneinfo/$SA_TIME_ZONE /etc/localtime && echo $SA_TIME_ZONE > /etc/timezone

# Set the working directory to root
WORKDIR /

CMD ["/entrypoint.sh"]