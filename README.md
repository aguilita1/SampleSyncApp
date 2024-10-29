# SampleSyncApp [![PHP Composer](https://github.com/aguilita1/SampleSyncApp/actions/workflows/php.yml/badge.svg?branch=main&event=push)](https://github.com/aguilita1/SampleSyncApp/actions/workflows/php.yml) [![PHP Version](https://img.shields.io/badge/PHP-v8.3-blue)](https://www.php.net/ChangeLog-8.php) [![Alpine Linux Version](https://img.shields.io/badge/Alpine_Linux-v3.20-blue)](https://alpinelinux.org/releases/) [![Composer Version](https://img.shields.io/badge/Composer-v2.8-blue)](https://github.com/composer/composer/releases)
A reference implementation to demonstrate how to use Github Actions with a simple PHP CLI synchronization application.
* Published Docker Images: [https://hub.docker.com/r/luigui/samplesyncapp](https://hub.docker.com/r/luigui/samplesyncapp)

## Demonstrates Various Continuous Integration Stages
* Checkout Code ``actions/checkout@v4``
* Validate composer.json and composer.lock  ``run: composer validate --strict``
* Cache Dev Composer dependencies ``actions/cache@v4``
* Install DEV Dependencies ``php-actions/composer@v6``
* PHP Static Analysis ``php-actions/phpstan@v3``
* PHPUnit Tests ``php-actions/phpunit@v4``
* Install Prod Dependencies ``php-actions/composer@v6``
* Setup Docker Buildx ``docker/setup-buildx-action@v3``
* Docker Meta ``docker/metadata-action@v5``
* Login to Docker Hub ``docker/login-action@v3``
* Build and Push Docker Image ``docker/build-push-action@v6``
* Docker Scout ``docker/scout-action@v1``
* Upload SARIF result ``github/codeql-action/upload-sarif@v3``
