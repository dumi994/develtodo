# Feature: Ticket — Guida Implementazione

## Obiettivo

Ticket inseriti manualmente con priorità, categoria, stato e note. **Nessuna chat integrata.**

## Dipendenze

Feature 01 (Layout), Modello Eloquent `Ticket`

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Controller + Migration

## Output atteso

- Lista ticket con badge priorità (🔴 Alta / 🟡 Media / 🟢 Bassa), categoria (🐛 Bug / ✨ Feature / ❓ Supporto), stato (Aperto/In lavorazione/Chiuso)
- Modale creazione ticket: cliente, oggetto, priorità, categoria, note
- Dettaglio espandibile: cliente, categoria, priorità, note, ultimo aggiornamento
- Dropdown cambio stato
- Avviso per ticket non aggiornati da > 3 giorni

## Guida passo-passo

### Passo 1 — Database

1. Migration: id, user_id, client, subject, priority (enum), category (enum), status (enum), notes (text), last_update (date)
2. Priority: 'alta', 'media', 'bassa'
3. Category: 'bug', 'feature', 'supporto'
4. Status: 'aperto', 'in_lavorazione', 'chiuso'

### Passo 2 — Modello

1. `php artisan make:model Ticket`
2. `$fillable`, `$casts`
3. `belongsTo(User)`

### Passo 3 — Controller

1. `php artisan make:controller TicketController --resource`
2. CRUD standard

### Passo 4 — Vista

1. `resources/views/tickets/index.blade.php`
2. Lista ticket: clic per espandere dettaglio (Alpine `selectedTicket`)
3. Dettaglio: griglia 2 colonne con info, note in box grigio
4. Dropdown cambio stato
5. Pulsante "+ Nuovo ticket" → modale creazione

### Passo 5 — Rotta

1. `Route::resource('tickets', TicketController::class)`

## Files da creare/modificare

- `database/migrations/xxxx_create_tickets_table.php`
- `app/Models/Ticket.php`
- `app/Http/Controllers/TicketController.php`
- `resources/views/tickets/index.blade.php`
- `routes/web.php`

## Status

[ ] Non iniziata
