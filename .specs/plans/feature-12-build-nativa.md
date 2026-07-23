# Feature: Build Nativa Windows — Guida Implementazione

## Obiettivo

Compilare l'applicazione Laravel in un eseguibile Windows (.exe) tramite NativePHP.

## Dipendenze

Feature 01-08 (tutte le funzionalità devono essere implementate e testate)

## Stack

NativePHP Desktop (comando `php artisan native:build`)

## Output atteso

- File .exe installabile in `dist/`
- App nativa Windows con PHP runtime embedded
- Database SQLite incluso nell'installazione
- Icona personalizzata DevelTodo
- (Opzionale) Installer NSIS o Squirrel

## Guida passo-passo

### Passo 1 — Requisiti

1. PHP 8.3+ installato localmente
2. Node.js 22+ installato
3. Composer dipendenze aggiornate

### Passo 2 — Configurazione build

1. In `config/nativephp.php`, imposta:
   - `version` → versione corrente (es. '1.0.0')
   - `app_id` → es. 'com.develtodo.app'
   - `author` → il tuo nome
   - `description` → 'DevelTodo - Todo e gestionale per web developer'
   - `website` → URL del progetto

### Passo 3 — Build

1. Esegui: `php artisan native:build`
2. Prima build: potrebbe richiedere download di Electron
3. Output: `dist/DevelTodo Setup 1.0.0.exe`

### Passo 4 — Test

1. Installa il .exe su Windows
2. Verifica che l'app funzioni offline
3. Verifica che SQLite sia accessibile

### Passo 5 — Distribuzione

1. Carica il .exe su GitHub Releases
2. Collega all'auto-updater (Feature 11)

## Files da creare/modificare

- `config/nativephp.php` — configurazioni di build
- `package.json` (generato da NativePHP)

## Status

[ ] Non iniziata
