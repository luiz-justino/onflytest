# 🚗 Microsserviço de Pedidos de Viagem Corporativa

Este é um microsserviço desenvolvido em **Laravel 12** para gerenciar pedidos de viagem corporativa. O sistema expõe uma API REST completa com autenticação JWT, permitindo que usuários criem, consultem e gerenciem seus pedidos de viagem.

---

## 🚀 Funcionalidades

### ✅ Implementadas
- **Autenticação JWT**: Sistema completo de login/registro com tokens
- **CRUD de Pedidos**: Criar, consultar, atualizar e cancelar pedidos de viagem
- **Controle de Status**: Aprovação e cancelamento de pedidos
- **Filtros Avançados**: Por status, destino, período e datas
- **Notificações**: Email automático para aprovação/cancelamento (via Mailpit)
- **Validação Completa**: Validação de dados e tratamento de erros
- **Testes Automatizados**: Cobertura completa com PHPUnit
- **Docker**: Ambiente containerizado para desenvolvimento
- **Segurança**: Usuários só podem ver/editar seus próprios pedidos

---

## 🚦 Início Rápido

### 🐳 Subindo o Projeto

```bash
git clone SEU_REPOSITORIO.git
cd onflytest
docker-compose up -d
./setup.sh
```

---

## 🌐 Acesso

- **Aplicação:** [http://localhost:8000](http://localhost:8000)
- **API Base:** [http://localhost:8000/api](http://localhost:8000/api)
- **PhpMyAdmin:** [http://localhost:8080](http://localhost:8080)
- **Mailpit (visualização de e-mails):** [http://localhost:8025](http://localhost:8025)

---

## 📚 Documentação da API

### 🔑 Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Login de usuário
- `POST /api/logout` - Logout
- `GET /api/me` - Perfil do usuário autenticado

### ✈️ Pedidos de Viagem
- `GET /api/travel-orders` - Listar pedidos do usuário
- `POST /api/travel-orders` - Criar novo pedido
- `GET /api/travel-orders/{id}` - Ver pedido específico
- `POST /api/travel-orders/{id}/cancel` - Cancelar pedido

### 🛡️ Admin (Aprovação/Cancelamento)
- `GET /api/admin/travel-orders` - Listar todos os pedidos
- `PATCH /api/admin/travel-orders/{id}/status` - Atualizar status

---

### 💡 Exemplos de Uso

#### Registrar usuário:
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@empresa.com",
    "password": "senha123",
    "password_confirmation": "senha123"
  }'
```

#### Login:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao@empresa.com",
    "password": "senha123"
  }'
```

#### Criar pedido de viagem:
```bash
curl -X POST http://localhost:8000/api/travel-orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{
    "requester_name": "João Silva",
    "destination": "São Paulo",
    "departure_date": "2024-12-15",
    "return_date": "2024-12-20"
  }'
```

---

## 📋 Comandos Úteis

### 🐳 Gerenciamento de Containers
```bash
docker-compose up -d
docker-compose down
docker-compose logs -f
```

### ⚙️ Comandos Artisan
```bash
docker-compose exec app php artisan [comando]

# Exemplos:
docker-compose exec app php artisan --version
docker-compose exec app php artisan route:list
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed --class=AdminUserSeeder
```

### 🖥️ Acesso ao Container
```bash
docker-compose exec app bash
```

---

## 🗂️ Estrutura do Projeto

```
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── mysql/
│       └── my.cnf
├── docker-compose.yml
├── dockerfile
├── setup.sh
├── .env.example
└── README.md
```

---

## ⚙️ Configurações de Ambiente

### 📄 Variáveis de Ambiente (.env/.env.example)

As principais variáveis já estão configuradas no `.env.example`:

```
APP_NAME=OnflyTest
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

CACHE_DRIVER=file
SESSION_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=admin@onflytest.com
MAIL_FROM_NAME="OnflyTest"

JWT_SECRET=
JWT_TTL=60
```

> **Obs:** O arquivo `.env` é criado automaticamente a partir do `.env.example` pelo `setup.sh`.

---

## 📧 Serviço de E-mail (Mailpit)

- O projeto utiliza o [Mailpit](https://github.com/axllent/mailpit) para capturar e visualizar e-mails enviados em desenvolvimento.
- **Acesse a interface web do Mailpit:**  
  [http://localhost:8025](http://localhost:8025)
- Porta SMTP utilizada pelo Laravel: `1025`

---

## 🔐 JWT_SECRET

O segredo JWT (`JWT_SECRET`) é utilizado para assinar e validar os tokens de autenticação da API.  
**Durante o setup, o comando abaixo é executado automaticamente:**

```bash
docker-compose exec app php artisan jwt:secret --force
```

Isso garante que o `.env` já estará configurado corretamente para autenticação JWT.

---

## 👤 Seeder de Usuário Administrador

Para criar um usuário administrador padrão, execute:

```bash
docker-compose exec app php artisan db:seed --class=AdminUserSeeder
```

- **Email:** admin@onflytest.com
- **Senha:** admin123

---

## 🔔 Notificações

O sistema utiliza notificações do Laravel. Certifique-se de rodar:

```bash
docker-compose exec app php artisan notifications:table
docker-compose exec app php artisan migrate
```

---

## 🛡️ Permissões de Alteração de Status

- Apenas usuários administradores (`is_admin = 1`) ou o próprio dono do pedido podem alterar o status do travel order.
- Caso contrário, a API retorna erro 403 (proibido).

---

## 🧪 Executar Testes

```bash
docker-compose exec app php artisan test
docker-compose exec app php artisan test --filter=TravelOrderTest
```

---

## 🔧 Solução de Problemas

### Erro "No application encryption key has been specified"
```bash
docker-compose exec app php artisan key:generate
```

### Erro de Permissão
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Limpar Cache
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
```

---

## 📦 Publicar no GitHub

```bash
git init
git add .
git commit -m "Initial commit: Laravel 12 with Docker setup"
git remote add origin https://github.com/seu-usuario/seu-repositorio.git
git branch -M main
git push -u origin main
```

---

## 📝 Notas

- **Laravel 12** com PHP 8.3
- **Docker** para ambiente isolado
- **MySQL** como banco de dados principal
- **Mailpit** para e-mails de desenvolvimento
- **JWT** para autenticação stateless
- **PHPUnit** para testes automatizados
- **Notificações** por email para eventos importantes

---