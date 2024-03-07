#!/bin/sh

# Configure PHP session.gc_maxlifetime
sed -i "s/session.gc_maxlifetime = .*$/session.gc_maxlifetime = $SA_PHP_SESSION_GC_MAXLIFETIME/" /etc/php82/php.ini
sed -i "s/max_execution_time = .*$/max_execution_time = $SA_PHP_MAX_EXECUTION_TIME/" /etc/php82/php.ini
sed -i "s/memory_limit = .*$/memory_limit = $SA_PHP_MEMORY_LIMIT/" /etc/php82/php.ini

echo " * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *"
echo " * Sample Sync Application written in PHP to test out Github Actions.*"
echo " * Copyright (C) 2024 Daniel Kelley                                  *"
echo " * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *"

/usr/bin/php82 /opt/ir/lib/main.php