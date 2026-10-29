FROM php:8.3-cli-alpine

# Injeta as variáveis de proxy para permitir o download no Alpine
ARG HTTP_PROXY
ARG HTTPS_PROXY
ENV http_proxy=${HTTP_PROXY}
ENV https_proxy=${HTTPS_PROXY}

# Instala a extensão do MySQL no PHP
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

# Inicia o servidor embutido do Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
