# DevelTodo

Applicazione **desktop per web developer freelance**: progetti, task/todo, ticket clienti, preventivi e fatture in un unico strumento, offline-first.

**Stack:** Laravel 11+ · PHP 8.2+ · NativePHP Desktop 2 (Electron) · SQLite · Blade + Tailwind CSS + Alpine.js · Vite · Laravel Breeze

## Funzionalità

- **Dashboard** — riepilogo di progetti attivi, ticket in sospeso, prossime scadenze, fatture in ritardo e preventivi in attesa
- **Progetti** — CRUD completo con workflow di stato (in arrivo → in corso → in revisione → completato → archiviato), ore stimate e colori
- **Task / Todo** — task associabili a progetti o liberi, priorità, scadenze e timer
- **Ticket** — inseriti manualmente, con cliente, priorità, categoria (bug/feature/supporto) e stato
- **Preventivi** — upload PDF, importo e workflow (bozza → inviato → accettato/rifiutato → fatturato)
- **Fatture** — promemoria di invio e workflow (da fare → inviata → pagata → sollecito)
- **Notifiche** — globali per scadenze e attività in attesa
- **Ricerca globale** — task, progetti, ticket, preventivi e fatture dall'header

## Requisiti

- PHP 8.2+
- Composer
- Node 20+ (solo per gli asset)

## Setup (sviluppo web)

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
npm run dev        # asset in watch (Vite)
php artisan serve  # http://localhost:8000
```

Al primo avvio l'app reindirizza a `/setup` (creazione dell'utente admin), poi alla dashboard.

## Test

```bash
php artisan test
```

La suite vive in `tests/` (Feature + Unit) e in `phpunit.xml`.

## Build desktop (Windows)

Gli asset vanno compilati prima del confezionamento nativo:

```bash
npm run build
php artisan native:build win --no-interaction
```

La build di release multi-piattaforma (Windows/macOS/Linux) è gestita dal workflow CI in `.github/workflows/build-release.yml` e pubblicata su GitHub Releases.

## Roadmap

La tabella di marcia è in `.specs/plans/` (feature-01 → feature-12).

## Documentazione

- `PROJECT_BRIEF.md` — brief funzionale
- `ADR.md` — architettura e decisioni
- `AGENT_FLOW.md` — workflow di sviluppo per gli agenti