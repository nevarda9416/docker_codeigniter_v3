# PHP 7.3 is the highest officially safe target for CI3
FROM php:7.3-apache

# Install system deps if needed and PHP extensions
RUN apt-get update && apt-get install -y \
        libzip-dev libicu-dev libonig-dev \
        && docker-php-ext-install mysqli pdo pdo_mysql \
        && a2enmod rewrite \
        && rm -rf /var/lib/apt/lists/*

# Optional: tune Apache docroot via build-arg / env \
ARG APACHE_DOCUMENT_ROOT=/var/www/html
ENV APACHE_DOCUMENT_ROOT=${APACHE_DOCUMENT_ROOT}

# Repoint Apache DocumentRoot and <Directory> to project root (or /public if you use it)
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
        && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copy a vhost with AllowOverride All to enable .htaccess (overrides above if presend)
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Recommended permissions for CI3 writable folders
# (adjust if you writable folders differ)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data || true
