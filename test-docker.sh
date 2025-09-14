#!/bin/bash

# Script para testar Docker com Laravel (versão offline)
echo "🚀 Testando configuração Docker..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Inicie o Docker primeiro."
    exit 1
fi

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker compose down 2>/dev/null

# Construir apenas o container da aplicação (que já temos)
echo "🔨 Construindo container da aplicação..."
docker compose build app

# Iniciar apenas os serviços básicos
echo "🚀 Iniciando serviços..."
docker compose up -d app nginx

echo "✅ Containers iniciados!"
echo ""
echo "🌐 Teste a aplicação em: http://localhost:8000"
echo ""
echo "📋 Comandos úteis:"
echo "  - Ver logs: docker compose logs -f"
echo "  - Parar: docker compose down"
echo "  - Acessar container: docker compose exec app bash"

