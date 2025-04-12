# Stage 1
# Run composer
FROM composer:2.8.8 AS composer
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
# Extend from official PHP image using latest Alpine parent image
FROM php:8.4.6-cli-alpine

# Added meta-data about this app
ARG APP_VERSION="1.2.3"
LABEL com.github.aguilita1.app-version=$APP_VERSION  \
      com.github.aguilita1.is-beta="false" \
      com.github.aguilita1.is-production="true" \
      org.opencontainers.image.vendor="aguilita1" \
      org.opencontainers.image.authors="Daniel.Ian.Kelley@gmail.com" 

# Install bash, and time zone data programs.
RUN apk update && apk upgrade && apk add \
    bash \
    tzdata \
    && rm -rf /var/cache/apk/*

# Create a non-root user and group (e.g., appuser with UID and GID 1000)
RUN addgroup -S appgroup && adduser -S appuser -G appgroup

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

# Map the source files into /opt/ir
RUN mkdir -p /opt/ir
COPY --from=composer /app /opt/ir

# Change owner and permissions on startup file
# Write App version to settings.php file
# Set Time Zone
# Move php.ini to config directory
RUN chown -R appuser:appgroup /opt/ir && \
    chmod +x /entrypoint.sh && \
    sed -i "s/'APP_VERSION'/'$APP_VERSION'/g" /opt/ir/lib/main.php && \
    ln -snf /usr/share/zoneinfo/$SA_TIME_ZONE /etc/localtime && echo $SA_TIME_ZONE > /etc/timezone && \
    mv php.ini-production /usr/local/etc/php/php.ini

# Modify php.ini to set memory and execution variables during build (run as root)
USER root
RUN sed -i "s/session.gc_maxlifetime = .*$/session.gc_maxlifetime = $SA_PHP_SESSION_GC_MAXLIFETIME/" /usr/local/etc/php/php.ini && \
    sed -i "s/max_execution_time = .*$/max_execution_time = $SA_PHP_MAX_EXECUTION_TIME/" /usr/local/etc/php/php.ini && \
    sed -i "s/memory_limit = .*$/memory_limit = $SA_PHP_MEMORY_LIMIT/" /usr/local/etc/php/php.ini

# Switch to the non-root user
USER appuser

# Set the working directory to root
WORKDIR /

CMD ["/entrypoint.sh"]