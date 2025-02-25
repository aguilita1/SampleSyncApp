# Stage 1
# Run composer
FROM  composer:2.8.5 AS composer
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
FROM php:8.4.3-cli-alpine3.20

# Added meta-data about this app
ARG APP_VERSION="1.1.1"
LABEL vendor=REVOLVE \
      maintainer="Daniel.Ian.Kelley@gmail.com" \
      description="Sample Sync App is a reference implementation to demonstrate how to use Github Actions with a simple PHP CLI synchronization application." \
      com.github.aguilita1.is-beta="false" \
      com.github.aguilita1.is-production="true" \
      com.github.aguilita1.version=$APP_VERSION \
      com.github.aguilita1.release-date="2025-02-08"

# Install bash, and time zone data programs.
RUN apk update && apk upgrade && apk add \
    bash \
    tzdata \
    && rm -rf /var/cache/apk/*

# Add all necessary config files (entrypoint.sh & php.ini) in one layer
ADD docker/ /

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

# Change owner and permissions on startup file
# Write App version to settings.php file
# Set Time Zone
# Move php.ini to config directory
RUN chown -R nobody /opt/ir && \
    chmod +x /entrypoint.sh && \
    sed -i "s/.*app_version = 'APP_VERSION'.*$/\$app_version = '$APP_VERSION';/" /opt/ir/lib/main.php && \
    ln -snf /usr/share/zoneinfo/$SA_TIME_ZONE /etc/localtime && echo $SA_TIME_ZONE > /etc/timezone && \
    mv php.ini-production /usr/local/etc/php/php.ini

# Set the working directory to root
WORKDIR /

CMD ["/entrypoint.sh"]