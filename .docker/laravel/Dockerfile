FROM serversideup/php:8.4-fpm-nginx-alpine

# Switch to root so we can do root things
USER root

# Install the intl and bcmath extensions with root permissions
RUN install-php-extensions rdkafka protobuf swoole intl

# Drop back to our unprivileged user
USER www-data