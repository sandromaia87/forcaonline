FROM php:8.3-cli-alpine

# Instala extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

# Copia TODO o código da sua máquina para dentro da imagem
COPY . .

# Ajusta permissões essenciais do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Inicia o servidor na porta 80 (conforme mapeado no seu docker-compose)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
