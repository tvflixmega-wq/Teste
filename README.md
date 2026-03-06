# StreamSaaS PHP 8 + MySQL

## Estrutura
- `install/` instalador web
- `config/` configuração gerada
- `core/` bootstrap, router, segurança, DB
- `controllers/`, `models/`, `views/` MVC principal
- `admin/` painel administrativo + TMDB + recursos extras
- `api/` API interna JSON
- `assets/` CSS/JS/imagens + 3 temas
- `sql/schema.sql` banco completo
- `logs/`, `cache/`, `uploads/`

## Instalação
1. Aponte o DocumentRoot para este diretório.
2. Execute `http://seu-dominio/install/install.php`.
3. Informe credenciais do banco e admin inicial.
4. Após concluir, o arquivo `install/installed.lock` bloqueia reinstalação.

## Deploy Apache
```apache
<VirtualHost *:80>
  ServerName stream.local
  DocumentRoot /var/www/stream
  <Directory /var/www/stream>
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```

## Deploy Nginx
```nginx
server {
  listen 80;
  server_name stream.local;
  root /var/www/stream;
  index index.php;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
  }
}
```

## TMDB
Adicione `tmdb.api_key` no instalador ou em `config/config.php`.
Acesse `/admin/tmdb.php` para importar por poster.

