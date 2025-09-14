<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel 12 com Docker</title>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
                margin: 0;
                padding: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                text-align: center;
                background: white;
                padding: 3rem;
                border-radius: 15px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                max-width: 600px;
                margin: 2rem;
            }
            h1 {
                color: #2d3748;
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            .subtitle {
                color: #4a5568;
                font-size: 1.2rem;
                margin-bottom: 2rem;
            }
            .status {
                background: #48bb78;
                color: white;
                padding: 1rem 2rem;
                border-radius: 8px;
                display: inline-block;
                font-weight: bold;
                margin: 1rem 0;
            }
            .info {
                background: #edf2f7;
                padding: 1.5rem;
                border-radius: 8px;
                margin: 2rem 0;
                text-align: left;
            }
            .info h3 {
                color: #2d3748;
                margin-top: 0;
            }
            .info ul {
                list-style: none;
                padding: 0;
            }
            .info li {
                padding: 0.5rem 0;
                border-bottom: 1px solid #e2e8f0;
            }
            .info li:last-child {
                border-bottom: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚀 Laravel 12</h1>
            <div class="subtitle">Funcionando com Docker!</div>
            
            <div class="status">✅ Aplicação rodando com sucesso</div>
            
            <div class="info">
                <h3>📋 Informações do Ambiente</h3>
                <ul>
                    <li><strong>PHP:</strong> {{ PHP_VERSION }}</li>
                    <li><strong>Laravel:</strong> {{ app()->version() }}</li>
                    <li><strong>Ambiente:</strong> {{ app()->environment() }}</li>
                    <li><strong>Debug:</strong> {{ config('app.debug') ? 'Ativado' : 'Desativado' }}</li>
                </ul>
            </div>
            
            <div class="info">
                <h3>🌐 Serviços Disponíveis</h3>
                <ul>
                    <li><strong>Aplicação:</strong> http://localhost:8000</li>
                    <li><strong>PhpMyAdmin:</strong> http://localhost:8080</li>
                    <li><strong>Redis:</strong> localhost:6379</li>
                </ul>
            </div>
            
            <p style="color: #718096; margin-top: 2rem;">
                Projeto configurado com Docker, Nginx, MySQL e Redis
            </p>
        </div>
    </body>
</html>

