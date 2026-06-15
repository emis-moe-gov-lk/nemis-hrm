# -----------------------
# 1. Node build stage
# -----------------------
FROM node:20 AS node_builder

WORKDIR /app
COPY . .

RUN npm install
RUN npm run build


# -----------------------
# 2. PHP runtime stage
# -----------------------
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    git \
    curl \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files (used for production builds; dev uses volume mount)
COPY . .

RUN composer install --no-interaction --no-dev --optimize-autoloader || true

EXPOSE 9000

CMD ["php-fpm"]





