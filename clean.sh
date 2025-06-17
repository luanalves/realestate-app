#!/bin/bash

echo "🧹 Iniciando limpeza completa do sistema..."

echo "🔄 Limpando caches do Laravel..."
php artisan lighthouse:clear-cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan clear-compiled

echo "📋 Limpando logs da aplicação..."
# Limpar arquivos de log do Laravel
> storage/logs/laravel.log
find storage/logs/ -name "*.log" -type f -exec truncate -s 0 {} \;

echo "🚀 Otimizando aplicação..."
php artisan optimize

echo "✅ Limpeza completa finalizada!"
echo "📊 Status dos arquivos de log:"
ls -lah storage/logs/
