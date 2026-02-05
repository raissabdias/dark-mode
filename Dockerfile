# Usa a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# 1. Instalar dependências do sistema e extensões do PHP necessárias para o Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql bcmath intl opcache zip

# 2. Configurar o Apache para apontar para a pasta /public (Padrão Laravel)
# Usamos 'sed' para editar o arquivo de configuração padrão do Apache direto na imagem
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 3. Habilitar o mod_rewrite do Apache (essencial para rotas do Laravel)
RUN a2enmod rewrite

# 4. Instalar o Composer (Gerenciador de pacotes PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Definir o diretório de trabalho
WORKDIR /var/www/html

# 6. Copiar os arquivos do projeto para dentro do container
COPY . .

# 7. Rodar instalações (Backend e Frontend)
# --no-scripts evita erros antes do autoload estar pronto
RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN npm install && npm run build

# 8. Ajustar permissões (O Apache precisa escrever nessas pastas)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Comando que roda quando o container inicia
# Roda as migrations, cache e inicia o servidor Apache
CMD bash -c "php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground"

# Expõe a porta 80
EXPOSE 80