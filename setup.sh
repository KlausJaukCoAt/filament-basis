#!/bin/bash
echo "📦 Bereite Laravel-Verzeichnisse vor…"
mkdir -p bootstrap/cache storage/framework/views storage/framework/cache storage/logs
chmod -R 775 bootstrap/cache storage
echo "✅ Verzeichnisse sind bereit!"
cp .env.example .env
echo "🔧 .env-Datei erstellt!"
echo "🔑 Generiere Anwendungsschlüssel und cache die Konfiguration…"
php artisan key:generate
php artisan config:cache
php artisan migrate --seed 
php artisan storage:link
echo "🔑 Anwendungsschlüssel generiert und Konfiguration zwischengespeichert!"
echo "📦 Installiere Composer-Abhängigkeiten…"
composer install
echo "✅ Composer-Abhängigkeiten installiert!"
echo "🚀 Starte den Entwicklungsserver auf http://localhost:8000 … "
php artisan serve --host=localhost --port=8000
echo "✅ Entwicklungsserver läuft!"
echo "🎉 Setup abgeschlossen! Viel Erfolg bei der Entwicklung mit Laravel!"