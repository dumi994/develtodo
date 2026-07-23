# Feature: Auto-Updater — Guida Implementazione

## Obiettivo

Aggiornamento automatico dell'app desktop senza disinstallazione manuale.

## Dipendenze

Build nativa Windows (.exe)

## Stack

NativePHP updater integrato (config/nativephp.php) + GitHub Releases

## Output atteso

- Configurazione updater in `config/nativephp.php`
- Versione app tracciata con `NATIVEPHP_APP_VERSION`
- Provider: GitHub Releases (o S3/Spaces)
- Notifica all'utente quando una nuova versione è disponibile
- Download automatico e installazione al riavvio

## Guida passo-passo

### Passo 1 — Configurazione

1. In `config/nativephp.php`, abilita updater:

```php
'updater' => [
    'enabled' => env('NATIVEPHP_UPDATER_ENABLED', true),
    'default' => env('NATIVEPHP_UPDATER_PROVIDER', 'github'),
    'providers' => [
        'github' => [
            'driver' => 'github',
            'repo' => env('GITHUB_REPO'),          // es. 'develtodo'
            'owner' => env('GITHUB_OWNER'),         // es. 'tuouser'
            'token' => env('GITHUB_TOKEN'),         // token con accesso alle release
            'vPrefixedTagName' => env('GITHUB_V_PREFIXED_TAG_NAME', true),
            'private' => env('GITHUB_PRIVATE', false),
            'channel' => env('GITHUB_CHANNEL', 'latest'),
            'releaseType' => env('GITHUB_RELEASE_TYPE', 'draft'),
        ],
    ],
],
```

### Passo 2 — Versione

1. Imposta `NATIVEPHP_APP_VERSION` in `.env` (es. `1.0.0`)
2. Ogni rilascio: incrementa versione, crea tag su GitHub

### Passo 3 — GitHub Release

1. Crea una release su GitHub con tag `v1.0.0`, `v1.1.0`, ecc.
2. Carica il file `.exe` come asset della release
3. L'app rileva automaticamente la nuova versione

### Passo 4 — Test

1. Builda l'app: `php artisan native:build`
2. Installa .exe, poi crea una nuova release con versione superiore
3. Riavvia l'app → dovrebbe mostrare notifica aggiornamento

## Files da creare/modificare

- `config/nativephp.php` — sezione updater
- `.env` — variabili NATIVEPHP*UPDATER*\*

## Status

[ ] Non iniziata
