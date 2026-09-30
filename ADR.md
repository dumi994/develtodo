# Architecture Decision Record

**Progetto:** DevelTodo — Todo e gestionale per web developer
**Data:** 22/07/2026
**Autore:** dumi994 (Sh)
**Aggiornato:** 30/09/2026 (revisione post-audit)

## Decisione

Realizzare un'applicazione desktop nativa (Windows) per web developer freelance / piccoli team che unifichi in un unico strumento: gestione progetti, task/todo, ticket clienti, fatture con promemoria, preventivi con upload PDF, e dashboard riepilogativa con notifiche desktop per tutte le scadenze.

## Contesto

I web developer freelance gestiscono quotidianamente strumenti separati (todo list, CRM, fatturazione, ticketing) senza un punto di raccolta unico. DevelTodo nasce per centralizzare queste attività in una singola app desktop nativa, con una codebase PHP che in futuro potrà essere estesa a mobile (iOS/Android) tramite NativePHP Mobile.

## Piattaforme scelte

- **Frontend:** Laravel Blade + Tailwind CSS + Alpine.js (interattività lato client), asset gestiti con **Vite** (build JS/CSS)
- **Backend:** PHP 8.2+ / Laravel 11+
- **Desktop:** NativePHP Desktop 2 (basato su Electron)
- **Mobile (futuro):** NativePHP Mobile 3 (iOS/Android)
- **Database:** SQLite (offline-first, portabile, incluso nell'app)
- **Auth:** Laravel Breeze (autenticazione multi-utente)
- **UI Framework:** Tailwind CSS (utility-first, nessun componente JS pesante)
- **Interattività:** Alpine.js (leggero, reattivo)
- **Auto-updater:** NativePHP updater integrato (supporta GitHub Releases, S3, DigitalOcean Spaces)

## Componenti principali

1. **Dashboard** — Riepilogo con progetti attivi, ticket in sospeso, prossime scadenze, fatture in ritardo, preventivi in attesa
2. **Progetti** — CRUD completo con stato workflow (in arrivo → in corso → in revisione → completato → archiviato), date, ore stimate, colori e tag
3. **Task / Todo** — Task associabili a progetti o liberi, priorità (alta/media/bassa), scadenze con countdown, checklist, timer
4. **Ticket** — Inseriti manualmente dall'utente, con cliente, priorità, categoria (bug/feature/supporto), stato e note. Nessuna chat integrata.
5. **Preventivi** — Upload PDF, cliente, importo, stato workflow (bozza → inviato → accettato → rifiutato → fatturato), scadenza validità
6. **Fatture** — Promemoria invio, stato workflow (da fare → inviata → pagata → sollecito), storico per cliente/progetto
7. **Notifiche Globali** — Sistema di notifiche per task in scadenza, progetti in scadenza, fatture in ritardo, preventivi in attesa, ticket non aggiornati, promemoria personalizzati
8. **Time Tracking** — Tracciamento ore effettive su task/progetto, timer avviabile manualmente, confronto con ore stimate
9. **Ricerca Globale** — Barra di ricerca nell'header per trovare rapidamente task, progetti, ticket, preventivi, fatture
10. **Build Nativa Windows** — Compilazione in .exe distribuibile tramite NativePHP (build CI multi-OS: Windows/macOS/Linux)
11. **Auto-Updater** — Aggiornamento automatico dell'app via GitHub Releases senza re-installazione

## Decisioni architetturali

| Scelta                           | Alternativa                          | Motivo                                                                                                               |
| -------------------------------- | ------------------------------------ | -------------------------------------------------------------------------------------------------------------------- |
| **PHP + Laravel + NativePHP**    | Electron + JS/TS, Flutter, .NET MAUI | Singola codebase PHP per desktop e futuro mobile; sfrutta ecosistema Laravel (ORM, scheduling, notifiche, Breeze)    |
| **SQLite**                       | PostgreSQL, MySQL, MongoDB           | Database offline-first, zero configurazione, portabile con l'app, perfetto per app desktop locale                    |
| **Blade + Tailwind + Alpine.js** | React, Vue, Livewire, Inertia        | Massima leggerezza, interattività sufficiente con Alpine.js, coerenza con l'ecosistema Laravel; Vite solo per l'asset pipeline |
| **Ticket inseriti manualmente**  | Import automatico da email/API       | L'utente vuole controllo manuale sui ticket — niente automazioni                                                     |
| **Preventivo → Fattura**         | Sistemi separati                     | Quando un preventivo è "Accettato", il pulsante "Crea fattura" copia i dati automaticamente                          |
| **Notifiche NativePHP**          | Notifiche browser/email              | App desktop nativa → notifiche OS native (Windows toast) per esperienza integrata                                    |
| **Auto-updater NativePHP**       | Download manuale, winget, chocolatey | NativePHP supporta updater integrato (GitHub/S3/Spaces). L'utente riceve notifica quando c'è una nuova versione.     |

## Vincoli

- **Unica codebase PHP** per tutte le piattaforme (desktop ora, mobile in futuro)
- **Offline-first** — tutto il database è locale (SQLite), nessuna dipendenza cloud
- **Asset JS/CSS con Vite** — build richiesta prima del confezionamento desktop (`npm run build`)
- **Multi-utente** — supporto login con Laravel Breeze, dati separati per utente
- **Codice in inglese** (classi, variabili, ID, attributi), **commenti in italiano**
- **Nessun file PHP** nel prototipo statico (`prototipo.html`) — solo HTML, Tailwind e Alpine.js
- **Ogni azione deve essere chiara e immediata** — niente menu nascosti, niente flussi ambigui

## Cosa NON è in scope

- **Import automatico ticket** (da email, API, GitHub) — i ticket sono solo manuali
- **Cloud / sync remoto** — tutto locale, nessun backend cloud
- **Multi-dispositivo in tempo reale** — il sync desktop↔mobile è futuro
- **Gateway di pagamento** — la gestione fatture è solo promemoria, non pagamenti
- **Plugin system** — suggerito come extra, non pianificato nello sviluppo iniziale
- **Chat ticket** — rimossa perché sconnessa da qualsiasi servizio esterno

## Feature future pianificate

- Sync dati tra desktop e mobile
- Notifiche push mobile
- Condivisione progetti tra utenti
- Integrazione GitHub/GitLab per import issue
- Backup/restore automatico
- Template progetti ricorrenti