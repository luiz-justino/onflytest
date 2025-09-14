#!/bin/bash

# Script para iniciar Laravel com Docker (versão simples)
echo "🚀 Iniciando Laravel com Docker..."

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker compose -f docker-compose-simple.yml down 2>/dev/null

# Construir e iniciar container
echo "🔨 Construindo e iniciando container..."
docker compose -f docker-compose-simple.yml up -d --build

echo "✅ Container iniciado!"
echo ""
echo "🌐 Acesse: http://localhost:8000"
echo ""
echo "📋 Comandos úteis:"
echo "  - Ver logs: docker compose -f docker-compose-simple.yml logs -f"
echo "  - Parar: docker compose -f docker-compose-simple.yml down"
echo "  - Acessar container: docker compose -f docker-compose-simple.yml exec app bash"

