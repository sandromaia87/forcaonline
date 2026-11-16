#!/bin/bash
set -euo pipefail

SERVER_IP="10.203.16.131"
SERVER_USER="clti"
PROXY="${PROXY:-${HTTPS_PROXY:-${HTTP_PROXY:-}}}"

cleanup() {
    rm -f forcaonline_imagem.tar
}
trap cleanup EXIT

PROXY_ENV_ARGS=()
BUILD_PROXY_ARGS=()
if [ -n "$PROXY" ]; then
    PROXY_ENV_ARGS=(-e "HTTP_PROXY=$PROXY" -e "HTTPS_PROXY=$PROXY")
    BUILD_PROXY_ARGS=(--build-arg "HTTP_PROXY=$PROXY" --build-arg "HTTPS_PROXY=$PROXY")
fi

echo "1. Compilando Front-end e Construindo Imagem Docker..."
# Garante que o CSS/JS mais recente seja gerado antes de lacrar a imagem
docker run --rm -v "$(pwd)":/app -w /app "${PROXY_ENV_ARGS[@]}" node:22-alpine sh -c "npm run build"

# "Assa" o código permanentemente dentro da imagem
docker build "${BUILD_PROXY_ARGS[@]}" -t forcaonline-app:latest .

echo "2. Exportando a imagem para arquivo (tar)..."
docker save -o forcaonline_imagem.tar forcaonline-app:latest

echo "3. Transferindo para o servidor (pasta /tmp)..."
scp forcaonline_imagem.tar "$SERVER_USER@$SERVER_IP:/tmp/"
scp docker-compose.yml "$SERVER_USER@$SERVER_IP:/tmp/"

echo "4. Aplicando atualização remotamente via SSH..."
ssh -t "$SERVER_USER@$SERVER_IP" << 'EOF'
    set -e
    sudo mkdir -p /opt/forcaonline
    sudo mv /tmp/forcaonline_imagem.tar /opt/
    sudo mv /tmp/docker-compose.yml /opt/forcaonline/docker-compose.yml
    cd /opt/forcaonline

    sudo docker load -i /opt/forcaonline_imagem.tar
    sudo docker compose down
    sudo docker compose up -d

    # 1º Cria as tabelas (se houver alguma nova)
    sudo docker compose exec app php artisan migrate --force

    # 2º Limpa o cache com o banco já pronto
    sudo docker compose exec app php artisan optimize:clear

    sudo rm -f /opt/forcaonline_imagem.tar
    echo "Atualização blindada concluída no servidor!"
EOF

echo "5. Limpando pacotes locais..."
rm -f forcaonline_imagem.tar
