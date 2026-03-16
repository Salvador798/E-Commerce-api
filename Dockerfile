# Dockerfile
FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar código Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --optimize-autoloader

# Exponer el puerto
EXPOSE 9000

CMD ["php-fpm"]
