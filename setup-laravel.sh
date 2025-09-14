#!/bin/bash

# Script para criar projeto Laravel 12 usando Docker
# Execute: chmod +x setup-laravel.sh && ./setup-laravel.sh

echo "🚀 Criando projeto Laravel 12 com Docker..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Inicie o Docker primeiro."
    exit 1
fi

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker compose down 2>/dev/null || docker-compose down 2>/dev/null

# Construir apenas o container da aplicação primeiro
echo "🔨 Construindo container da aplicação..."
docker compose build app

# Criar projeto Laravel usando o container Docker
echo "📦 Criando projeto Laravel..."
docker run --rm -v $(pwd):/var/www composer create-project laravel/laravel . --prefer-dist

# Verificar se o projeto foi criado
if [ ! -f "composer.json" ]; then
    echo "❌ Erro ao criar projeto Laravel"
    exit 1
fi

echo "✅ Projeto Laravel criado com sucesso!"

# Copiar arquivo de ambiente
echo "📝 Configurando arquivo .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Construir e iniciar todos os containers
echo "🔨 Iniciando todos os containers..."
docker compose up -d --build

# Aguardar os containers estarem prontos
echo "⏳ Aguardando containers estarem prontos..."
sleep 30

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker compose exec app php artisan key:generate

# Executar migrações
echo "🗃️ Executando migrações..."
docker compose exec app php artisan migrate

# Limpar cache
echo "🧹 Limpando cache..."
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear

# Criar link simbólico para storage
echo "🔗 Criando link simbólico para storage..."
docker compose exec app php artisan storage:link

echo "✅ Setup concluído!"
echo ""
echo "🌐 Acesse sua aplicação em: http://localhost:8000"
echo "🗄️ PhpMyAdmin disponível em: http://localhost:8080"
echo "📊 Redis disponível em: localhost:6379"
echo ""
echo "📋 Comandos úteis:"
echo "  - Parar containers: docker compose down"
echo "  - Ver logs: docker compose logs -f"
echo "  - Executar comandos artisan: docker compose exec app php artisan [comando]"
echo "  - Acessar container: docker compose exec app bash"

