
## Requisitos

- PHP 8.1+ (assumido; se você usa uma versão mais nova do PHP, a aplicação deverá funcionar)  
- Composer 2.x  
- Extensões PHP recomendadas: pdo_mysql, pdo, mbstring, openssl, json, fileinfo  
- Servidor web (Apache/Nginx) ou o servidor embutido do PHP para desenvolvimento

Observação: O arquivo `composer.json` declara dependências como `illuminate/database`, `vlucas/phpdotenv`, `phpmailer/phpmailer`, entre outras. Se você tiver requisitos específicos de versão do PHP na sua infra, ajuste conforme necessário.

## Instalação

1. Clone o repositório:

   git clone <https://github.com/JairoJeffersont/projeto.git> projeto
   cd projeto

2. Instale as dependências via Composer:

   composer install --no-dev --optimize-autoloader

3. Crie o arquivo de ambiente `.env` (veja a seção a seguir).

4. Crie a base de dados e importe o dump SQL (veja seção Banco de dados).

5. Ajuste permissões se necessário (diretórios de upload, cache ou logs que a aplicação precise escrever).

## Configuração (.env)

A aplicação usa variáveis de ambiente para configuração (via `vlucas/phpdotenv`). Crie um arquivo `.env` na raiz do projeto com valores semelhantes a este exemplo:

```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha

# Mail (usado por PHPMailer se configurado no código)
MAIL_HOST=smtp.exemplo.com
MAIL_PORT=587
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=nao-responda@exemplo.com
MAIL_FROM_NAME="Nome da Aplicação"
```

## Banco de dados

1. Crie um banco de dados vazio (MySQL/MariaDB). Exemplo:

   CREATE DATABASE nome_do_banco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

2. Importe o dump SQL fornecido em `sql/DB.sql`:

   mysql -u usuario -p nome_do_banco < sql/DB.sql

## Executando localmente

Opção rápida (servidor embutido do PHP):

```
php -S localhost:8000 -t public
```

Abra no navegador: http://localhost:8000

Opção com Apache/Nginx: aponte o DocumentRoot para a pasta `public/` do projeto. Garanta que as regras de rewrite (se houver) estejam configuradas para encaminhar requisições para `public/index.php`.

## Estrutura principal do projeto

- `public/` — raíz pública (index.php, assets, uploads).  
- `src/` — código fonte da aplicação (Controllers, Models, Views, Helpers).  
- `vendor/` — dependências gerenciadas pelo Composer.  
- `sql/DB.sql` — dump do banco para importar dados iniciais.  
- `composer.json` — dependências do projeto.

## Contribuição

1. Abra uma issue descrevendo a mudança.  
2. Crie um branch com um nome claro: `feature/nome-da-feature` ou `fix/descricao`.  
3. Envie um pull request com descrição e instruções para testar.

## Licença

Este projeto segue a licença MIT (conforme `composer.json`).

---

