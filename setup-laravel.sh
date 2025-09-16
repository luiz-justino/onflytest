#!/bin/bash

echo "⏳ Aguardando containers subirem..."
sleep 10

echo "📦 Instalando dependências do Composer..."
docker-compose exec app composer install

echo "📝 Copiando .env.example para .env (se necessário)..."
docker-compose exec app cp -n .env.example .env

echo "🔑 Gerando key do Laravel..."
docker-compose exec app php artisan key:generate

echo "🔐 Gerando JWT_SECRET..."
docker-compose exec app php artisan jwt:secret --force

echo "🗄️ Rodando migrations e seeders..."
docker-compose exec app php artisan migrate --seed

echo "🔔 Gerando tabela de notificações..."
docker-compose exec app php artisan notifications:table
docker-compose exec app php artisan migrate

echo "🧹 Limpando cache de configuração..."
docker-compose exec app php artisan config:cache

echo ""
echo "✅ Setup finalizado! Serviços disponíveis:"
echo "- App:        http://localhost:8000"
echo "- PhpMyAdmin: http://localhost:8080"
echo "- Mailpit:    http://localhost:8025"
echo ""
echo "👤 Usuário administrador padrão:"
echo "- Email: admin@onflytest.com"
echo "- Senha: admin123"
echo ""