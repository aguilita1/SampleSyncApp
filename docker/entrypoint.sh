#!/bin/sh

# Configure PHP session.gc_maxlifetime
 sed -i "s/session.gc_maxlifetime = .*$/session.gc_maxlifetime = $SA_PHP_SESSION_GC_MAXLIFETIME/" /usr/local/etc/php/php.ini
 sed -i "s/max_execution_time = .*$/max_execution_time = $SA_PHP_MAX_EXECUTION_TIME/" /usr/local/etc/php/php.ini
 sed -i "s/memory_limit = .*$/memory_limit = $SA_PHP_MEMORY_LIMIT/" /usr/local/etc/php/php.ini

echo " * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *"
echo " * Sample Sync Application written in PHP to test out Github Actions.*"
echo " * Copyright (C) 2024 Daniel Kelley                                  *"
echo " * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *"

php /opt/ir/lib/main.php
