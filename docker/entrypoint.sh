#!/bin/sh
set -e

cd /var/www/html

# Desenvolvimento local (código montado como volume): prepara .env e dependências
if [ "$APP_ENV" = "local" ]; then
    [ -f .env ] || cp .env.example .env
    [ -f vendor/autoload.php ] || composer install --no-interaction --prefer-dist
    grep -q '^APP_KEY=base64' .env || php artisan key:generate --force
fi

if [ -z "$APP_KEY" ] && ! grep -q '^APP_KEY=base64' .env 2>/dev/null; then
    echo "ERRO: defina a variável APP_KEY (gere com: php artisan key:generate --show)" >&2
    exit 1
fi

[ -d public/vendor/adminlte ] || php artisan adminlte:install --only=assets --force --no-interaction

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/fonts bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Aguarda o banco ficar disponível e roda as migrations
tentativas=0
until php artisan migrate --force; do
    tentativas=$((tentativas + 1))
    if [ "$tentativas" -ge 20 ]; then
        echo "ERRO: não foi possível conectar ao banco de dados." >&2
        exit 1
    fi
    echo "Aguardando o banco de dados... ($tentativas)"
    sleep 3
done

php artisan db:seed --force

if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan optimize:clear
fi

exec "$@"
