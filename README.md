# Projeto

Este repositório contém uma aplicação PHP simples estruturada com um diretório `public/` como raiz pública. Este README descreve como instalar, configurar e executar a aplicação em ambiente local.

## Sumário

- Requisitos
- Instalação
- Configuração (.env)
- Banco de dados
- Executando localmente
- Resolução de problemas comuns
- Estrutura principal do projeto
- Contribuição

## Requisitos

- PHP 8.1+ (assumido; se você usa uma versão mais nova do PHP, a aplicação deverá funcionar)  
- Composer 2.x  
- Extensões PHP recomendadas: pdo_mysql, pdo, mbstring, openssl, json, fileinfo  
- Servidor web (Apache/Nginx) ou o servidor embutido do PHP para desenvolvimento

Observação: O arquivo `composer.json` declara dependências como `illuminate/database`, `vlucas/phpdotenv`, `phpmailer/phpmailer`, entre outras. Se você tiver requisitos específicos de versão do PHP na sua infra, ajuste conforme necessário.

## Instalação

1. Clone o repositório:

   git clone <url-do-repositorio> projeto
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

Observação: Não existe um arquivo `.env.example` no repositório; crie o `.env` manualmente seguindo o template acima.

## Banco de dados

1. Crie um banco de dados vazio (MySQL/MariaDB). Exemplo:

   CREATE DATABASE nome_do_banco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

2. Importe o dump SQL fornecido em `sql/DB.sql`:

   mysql -u usuario -p nome_do_banco < sql/DB.sql

Se preferir SQLite, você precisará ajustar a camada de conexão no código (configuração do `illuminate/database`) e providenciar o arquivo `.sqlite` apropriado.

## Executando localmente

Opção rápida (servidor embutido do PHP):

```
php -S localhost:8000 -t public
```

Abra no navegador: http://localhost:8000

Opção com Apache/Nginx: aponte o DocumentRoot para a pasta `public/` do projeto. Garanta que as regras de rewrite (se houver) estejam configuradas para encaminhar requisições para `public/index.php`.

## Resolução de problemas comuns

- Erro de classes não encontradas (Class not found): execute `composer dump-autoload` e verifique se o `vendor/` está presente.  
- Erros de conexão com o banco: confirme as variáveis no `.env` e se a extensão `pdo_mysql` está habilitada.  
- Permissões: se a aplicação grava arquivos (uploads, logs), certifique-se de que o diretório correspondente em `public/` ou `storage/` seja gravável pelo usuário do servidor web.  
- Mail não funciona: verifique as credenciais SMTP no `.env` e logs do servidor SMTP. A aplicação usa PHPMailer; se houver configuração adicional no código, ajuste conforme necessário.

Dica de debug: defina `APP_DEBUG=true` no `.env` para habilitar mensagens de erro detalhadas em ambiente local.

## Estrutura principal do projeto

- `public/` — raíz pública (index.php, assets, uploads).  
- `src/` — código fonte da aplicação (Controllers, Models, Views, Helpers).  
- `vendor/` — dependências gerenciadas pelo Composer.  
- `sql/DB.sql` — dump do banco para importar dados iniciais.  
- `composer.json` — dependências do projeto.

Arquivos importantes:
- `public/index.php` — ponto de entrada.  
- `src/Views/router.php` — roteamento de views dentro da aplicação.  
- `src/Helpers/SessionHelper.php` — manipulação de sessão.  

## Observações e suposições

- Assumi PHP 8.1+ como requisito mínimo. Se sua infra exigir outra versão, ajuste as dependências ou informe-me para eu atualizar este README com a versão exata.  
- Não foram encontradas migrations do framework; o banco é fornecido pelo arquivo `sql/DB.sql`.

## Contribuição

1. Abra uma issue descrevendo a mudança.  
2. Crie um branch com um nome claro: `feature/nome-da-feature` ou `fix/descricao`.  
3. Envie um pull request com descrição e instruções para testar.

## Licença

Este projeto segue a licença MIT (conforme `composer.json`).

---

