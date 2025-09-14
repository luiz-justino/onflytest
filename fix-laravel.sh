#!/bin/bash

# Script para corrigir problemas comuns do Laravel no Docker
echo "🔧 Corrigindo configuração do Laravel..."

# Instalar dependências do Composer
echo "📦 Instalando dependências do Composer..."
docker compose exec app composer install --no-interaction

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker compose exec app php artisan key:generate

# Corrigir permissões
echo "🔒 Corrigindo permissões..."
docker compose exec --user root app chown -R www:www /var/www/storage /var/www/bootstrap/cache
docker compose exec --user root app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Executar migrações
echo "🗃️ Executando migrações..."
docker compose exec app php artisan migrate

# Limpar caches
echo "🧹 Limpando caches..."
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear

# Criar link simbólico para storage
echo "🔗 Criando link simbólico para storage..."
docker compose exec app php artisan storage:link

echo "✅ Configuração corrigida com sucesso!"
echo ""
echo "🌐 Acesse: http://localhost:8000"
echo ""
echo "📋 Comandos artisan funcionando:"
echo "  - docker compose exec app php artisan --version"
echo "  - docker compose exec app php artisan route:list"
echo "  - docker compose exec app php artisan make:model User"
