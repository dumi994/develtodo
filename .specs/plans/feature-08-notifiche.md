# Feature: Notifiche Globali — Guida Implementazione

## Obiettivo

Sistema di notifiche desktop per tutte le scadenze (task, progetti, fatture, preventivi, ticket).

## Dipendenze

Feature 01 (Layout), Feature 03-07 (tutti i moduli con scadenze)

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + NativePHP Notifications API

## Output atteso

- Dropdown notifiche nell'header con campanella
- Badge contatore notifiche non lette
- Notifiche per: task in scadenza (24h prima), fatture in ritardo, preventivi in attesa da >7gg, ticket non aggiornati da >3gg, progetti in scadenza (7gg prima)
- Marcatura "letta" al click
- Pulsante "Segna tutte lette"
- Notifiche native desktop (NativePHP) in futuro

## Guida passo-passo

### Passo 1 — Database

1. Migration: `create_notifications_table` (o usa tabella Laravel native)
2. Campi: id, user_id, type (string), title, icon, read (boolean), link (nullable), created_at

### Passo 2 — Modello

1. `php artisan make:model Notification`
2. `$fillable`, `$casts['read' => 'boolean']`

### Passo 3 — Generazione notifiche

1. Crea un `NotificationService` con metodi:
   - `checkTaskDeadlines()`: task con due_date = oggi/domani
   - `checkOverdueInvoices()`: fatture overdue
   - `checkPendingQuotes()`: preventivi in attesa da >7gg
   - `checkStaleTickets()`: ticket non aggiornati da >3gg
   - `checkProjectDeadlines()`: progetti in scadenza
2. Ogni metodo crea record Notification se non già presente

### Passo 4 — Scheduler

1. In `app/Console/Kernel.php`, programma i check ogni ora:
   ```php
   $schedule->call(function() { app(NotificationService::class)->checkAll(); })->hourly();
   ```

### Passo 5 — Vista

1. Dropdown nell'header: `x-for="n in notifications"` con badge unread
2. Clic per marcare letta
3. "Tutte lette" -> `notifications.forEach(n => n.read = true)`

### Passo 6 — NativePHP (futuro)

1. Usa `Native\Desktop\Facades\Notification` per notifiche OS native
2. ```php
   Notification::title('Task in scadenza')
     ->body('Fix header scade oggi alle 18:00')
     ->show();
   ```

## Files da creare/modificare

- `database/migrations/xxxx_create_notifications_table.php`
- `app/Models/Notification.php`
- `app/Services/NotificationService.php`
- `app/Console/Kernel.php`
- `resources/views/components/notification-dropdown.blade.php`

## Status

[ ] Non iniziata
