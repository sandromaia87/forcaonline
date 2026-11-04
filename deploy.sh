#!/bin/bash

SERVER_IP="10.203.16.131"
SERVER_USER="clti"

echo "1. Empacotando arquivos locais..."
# Ocultado o 'v' do tar para não poluir a tela com a lista de arquivos
tar -czf forcaonline_deploy.tar.gz .

echo "2. Transferindo para o servidor (pasta /tmp)..."
scp forcaonline_deploy.tar.gz $SERVER_USER@$SERVER_IP:/tmp/

echo "3. Aplicando atualização remotamente via SSH..."
# O parâmetro -t força a alocação de terminal para o sudo pedir a senha corretamente
ssh $SERVER_USER@$SERVER_IP "sudo mv /tmp/forcaonline_deploy.tar.gz /opt/ && cd /opt/forcaonline && sudo tar -xzf /opt/forcaonline_deploy.tar.gz && sudo docker compose exec app php artisan optimize:clear && sudo rm -f /opt/forcaonline_deploy.tar.gz && echo 'Atualização concluída no servidor!'"

echo "4. Limpando pacote local..."
rm -f forcaonline_deploy.tar.gz
