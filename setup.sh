#!/bin/bash
echo "📦 Bereite Laravel-Verzeichnisse vor…"
mkdir -p bootstrap/cache storage/framework/views storage/framework/cache storage/logs
chmod -R 775 bootstrap/cache storage
echo "✅ Verzeichnisse sind bereit!"
echo "📦 Installiere Composer-Abhängigkeiten…"
composer install
echo "✅ Composer-Abhängigkeiten installiert!"
cp .env.example .env
echo "🔧 .env-Datei erstellt!"
echo "🔑 Generiere Anwendungsschlüssel und cache die Konfiguration…"
php artisan key:generate
php artisan storage:link
php artisan config:cache
php artisan migrate --seed 
echo "🎉 Setup abgeschlossen! Viel Erfolg bei der Entwicklung mit Laravel!"