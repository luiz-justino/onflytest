#!/bin/bash

# Script para ambiente de desenvolvimento
echo "🚀 Iniciando ambiente de desenvolvimento Laravel..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Inicie o Docker primeiro."
    exit 1
fi

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker compose -f docker-compose.dev.yml down 2>/dev/null

# Construir e iniciar containers
echo "🔨 Construindo e iniciando containers de desenvolvimento..."
docker compose -f docker-compose.dev.yml up -d --build

# Aguardar containers estarem prontos
echo "⏳ Aguardando containers estarem prontos..."
sleep 20

# Instalar dependências
echo "📦 Instalando dependências..."
docker compose -f docker-compose.dev.yml exec app composer install

# Gerar chave da aplicação se não existir
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    echo "🔑 Gerando chave da aplicação..."
    docker compose -f docker-compose.dev.yml exec app php artisan key:generate
fi

# Executar migrações
echo "🗃️ Executando migrações..."
docker compose -f docker-compose.dev.yml exec app php artisan migrate

# Limpar caches
echo "🧹 Limpando caches..."
docker compose -f docker-compose.dev.yml exec app php artisan config:clear
docker compose -f docker-compose.dev.yml exec app php artisan cache:clear
docker compose -f docker-compose.dev.yml exec app php artisan route:clear

# Criar link simbólico para storage
echo "🔗 Criando link simbólico para storage..."
docker compose -f docker-compose.dev.yml exec app php artisan storage:link

echo "✅ Ambiente de desenvolvimento iniciado!"
echo ""
echo "🌐 Aplicação: http://localhost:8000"
echo "🗄️ PhpMyAdmin: http://localhost:8080"
echo "📊 Redis: localhost:6379"
echo ""
echo "📋 Comandos úteis:"
echo "  - Ver logs: docker compose -f docker-compose.dev.yml logs -f"
echo "  - Parar: docker compose -f docker-compose.dev.yml down"
echo "  - Artisan: docker compose -f docker-compose.dev.yml exec app php artisan [comando]"
echo "  - Bash: docker compose -f docker-compose.dev.yml exec app bash"
echo "  - Composer: docker compose -f docker-compose.dev.yml exec app composer [comando]"
