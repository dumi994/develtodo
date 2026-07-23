# Feature: Time Tracking — Guida Implementazione

## Obiettivo

Tracciare le ore effettivamente spese su ogni task/progetto e confrontarle con le ore stimate.

## Dipendenze

Feature 03 (Progetti), Feature 04 (Task)

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Migration

## Output atteso

- Nella card progetto: ore spese / ore stimate (es. "32h / 40h")
- Nel dettaglio task: timer avviabile manualmente
- Pulsante "Avvia timer" su ogni task in corso
- Totale ore spese per progetto nella dashboard
- Statistiche tempo nella dashboard

## Guida passo-passo

### Passo 1 — Database

1. Aggiungi campo `actual_hours` (decimal, nullable) alla tabella `tasks`
2. Oppure crea tabella separata `time_entries`: id, task_id, user_id, start_at, end_at, duration (minuti)

### Passo 2 — Modello

1. Se tabella separata: `php artisan make:model TimeEntry`
2. Relazioni: `belongsTo(Task)`, `belongsTo(User)`

### Passo 3 — Timer Alpine.js

1. Pulsante "▶ Avvia" sul task → registra start_at
2. Pulsante "⏹ Ferma" → calcola durata, salva, mostra tempo
3. Mostra timer in tempo reale con `setInterval`

### Passo 4 — Vista

1. Nella lista task: colonna "Tempo" con ore spese
2. Nella card progetto: "32h / 40h (80%)"
3. Dashboard: card "Ore tracciate oggi/questa settimana"

### Passo 5 — Rotta

1. `Route::post('/tasks/{task}/timer/start', [TaskController::class, 'timerStart'])`
2. `Route::post('/tasks/{task}/timer/stop', [TaskController::class, 'timerStop'])`

## Files da creare/modificare

- `database/migrations/xxxx_add_actual_hours_to_tasks.php`
- `app/Models/TimeEntry.php` (opzionale)
- `resources/views/tasks/index.blade.php` (aggiungi colonna tempo)
- `resources/views/projects/index.blade.php` (aggiungi ore)

## Status

[ ] Non iniziata
