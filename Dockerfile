# Usa PHP 8.2
FROM php:8.2-apache

# 1. Instala dependências do sistema
# Adicionamos bibliotecas gráficas (libjpeg, libfreetype) essenciais para o Filament manipular imagens
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    nodejs \
    npm

# 2. Limpa cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Configura e Instala extensões do PHP
# O passo crucial: Configurar o GD para aceitar JPEG e FreeType
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache

# 4. Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Define diretório
WORKDIR /var/www/html

# 6. Copia arquivos
COPY . /var/www/html

# 7. Composer Install (Com flag para ignorar requisitos de plataforma se houver conflito menor)
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# 8. Frontend Build
RUN npm install && npm run build

# 9. Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Configuração do Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf
RUN a2enmod rewrite

# 11. Expor Porta
EXPOSE 80

# 12. Comando Final
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground