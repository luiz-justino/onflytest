# Laravel 12 com Docker

Este projeto está configurado para rodar Laravel 12 com Docker.

## 🚀 Início Rápido

### Para Desenvolvimento (Recomendado)
```bash
# Execute o script de desenvolvimento
./dev.sh
```

### Versão Simples (Para teste rápido)
```bash
# Execute o script simples
./start-simple.sh
```

### Versão Completa (com Nginx, MySQL, Redis)
```bash
# Execute o script completo
./setup-laravel.sh
```

## 🌐 Acesso

- **Aplicação:** http://localhost:8000
- **PhpMyAdmin:** http://localhost:8080 (apenas versão completa)
- **Redis:** localhost:6379 (apenas versão completa)

## 📋 Comandos Úteis

### Gerenciamento de Containers
```bash
# Desenvolvimento (recomendado)
docker compose -f docker-compose.dev.yml up -d
docker compose -f docker-compose.dev.yml down
docker compose -f docker-compose.dev.yml logs -f

# Versão simples
docker compose -f docker-compose-simple.yml up -d
docker compose -f docker-compose-simple.yml down
docker compose -f docker-compose-simple.yml logs -f

# Versão completa
docker compose up -d
docker compose down
docker compose logs -f
```

### Comandos Artisan
```bash
# Desenvolvimento (recomendado)
docker compose -f docker-compose.dev.yml exec app php artisan [comando]

# Versão simples
docker compose -f docker-compose-simple.yml exec app php artisan [comando]

# Versão completa
docker compose exec app php artisan [comando]

# Exemplos:
docker compose -f docker-compose.dev.yml exec app php artisan --version
docker compose -f docker-compose.dev.yml exec app php artisan route:list
docker compose -f docker-compose.dev.yml exec app php artisan make:model User
docker compose -f docker-compose.dev.yml exec app php artisan make:controller UserController
```

### Acesso ao Container
```bash
# Versão simples
docker compose -f docker-compose-simple.yml exec app bash

# Versão completa
docker compose exec app bash
```

## 🗂️ Estrutura do Projeto

```
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── mysql/
│       └── my.cnf
├── docker-compose.yml (versão completa)
├── docker-compose-simple.yml (versão simples)
├── dockerfile
├── start-simple.sh
├── setup-laravel.sh
└── env.example
```

## ⚙️ Configurações

### Versão Simples
- **PHP 8.3** com FPM
- **Cache/Sessão:** Arquivo
- **Porta:** 8000

### Versão Completa
- **PHP 8.3** com FPM
- **Nginx** como servidor web
- **MySQL 8.0** como banco de dados
- **Redis** para cache e sessões
- **PhpMyAdmin** para gerenciamento do banco

## 🔧 Solução de Problemas

### Problemas de Conectividade
Se você tiver problemas para baixar imagens Docker, use a versão simples:
```bash
./start-simple.sh
```

### Erro "No application encryption key has been specified"
Execute o script de correção:
```bash
./fix-laravel.sh
```

### Erro de Permissão
```bash
# Ajustar permissões
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Limpar Cache
```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
```

## 📦 Publicar no GitHub

### Primeiro commit
```bash
# Inicializar repositório Git
git init

# Adicionar todos os arquivos
git add .

# Fazer commit inicial
git commit -m "Initial commit: Laravel 12 with Docker setup"

# Adicionar repositório remoto (substitua pela sua URL)
git remote add origin https://github.com/seu-usuario/seu-repositorio.git

# Enviar para GitHub
git branch -M main
git push -u origin main
```

### Desenvolvimento contínuo
```bash
# Para desenvolvimento diário, use:
./dev.sh

# Para fazer commits:
git add .
git commit -m "Descrição das alterações"
git push
```

## 📝 Notas

- O projeto usa PHP 8.3 com todas as extensões necessárias
- Configuração otimizada para Laravel 12
- Scripts automatizados para facilitar o uso
- Três versões: desenvolvimento, simples (apenas PHP) e completa (com todos os serviços)
- Ambiente de desenvolvimento isolado com volumes persistentes