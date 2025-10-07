#!/bin/bash
echo "📦 Bereite Laravel-Verzeichnisse vor…"
mkdir -p bootstrap/cache storage/framework/views storage/framework/cache storage/logs
chmod -R 775 bootstrap/cache storage
echo "✅ Verzeichnisse sind bereit!"
echo "📦 Installiere Composer-Abhängigkeiten…"
composer install
echo "✅ Composer-Abhängigkeiten installiert!"
echo "Lösche bestehendes Git-Repository und initialisiere ein neues…"
rm -rf .git
git init
echo "✅ Neues Git-Repository initialisiert!"
echo "🔧 Erstelle .env-Datei und konfiguriere Anwendung…"
cp .env.example .env
echo "🔧 .env-Datei erstellt!"
echo "🔑 Generiere Anwendungsschlüssel und cache die Konfiguration…"
php artisan key:generate
php artisan storage:link
php artisan config:cache
echo "🎉 Setup abgeschlossen! Viel Erfolg bei der Entwicklung mit Laravel!"
echo "Next Steps:"
echo "-----------------------------------"
echo "1. Passe deine .env-Datei an, insbesondere die Datenbankeinstellungen."
echo "2. Erstelle eine neue Datenbank für dein Projekt."
echo "3. Führe die Migrationen und Seeder aus mit: php artisan migrate --seed"
echo "4. Starte den Entwicklungsserver mit: php artisan serve"
echo "5. Öffne deinen Browser und gehe zu: http://localhost:8000"
rm .setup.sh