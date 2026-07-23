# Feature: Progetti — Guida Implementazione

## Obiettivo

CRUD completo progetti con stato workflow, date, colori e pulsante di avanzamento rapido.

## Dipendenze

Feature 01 (Layout), Modello Eloquent `Project`

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Controller + Migration

## Output atteso

- Griglia di cards progetto (nome, cliente, stato, progresso, scadenza)
- Clic sulla card → modale modifica con campi precompilati
- Dropdown cambio stato (in_arrivo → in_corso → in_revisione → completato → archiviato)
- Pulsante "Avanza →" che avanza di uno stato
- Pulsante "+ Nuovo progetto" per crearne di nuovi
- Barra progresso colorata in base allo stato

## Guida passo-passo

### Passo 1 — Database (Migration)

1. Crea migration: `php artisan make:migration create_projects_table`
2. Campi: id, user_id, name, description, client, color, status (enum), start_date, due_date, estimated_hours, doneTasks (default 0), totalTasks (default 0)
3. Status enum: 'in_arrivo', 'in_corso', 'in_revisione', 'completato', 'archiviato'
4. Esegui: `php artisan migrate`

### Passo 2 — Modello

1. Crea: `php artisan make:model Project`
2. Aggiungi `$fillable` con tutti i campi
3. Aggiungi `$casts` per date e status
4. Relazione: `belongsTo(User::class)`

### Passo 3 — Controller

1. Crea: `php artisan make:controller ProjectController --resource`
2. index(): restituisce tutti i progetti dell'utente loggato
3. store(): valida e crea
4. update(): valida e aggiorna
5. destroy(): elimina

### Passo 4 — Vista Blade

1. Vista `resources/views/projects/index.blade.php`
2. Estende layout `app.blade.php`
3. Cicla `@foreach($projects as $p)` in cards
4. Mostra: pallino colore, badge stato, nome, cliente, scadenza, progresso

### Passo 5 — Interattività Alpine.js

1. Modale creazione/modifica con campi: nome, cliente, scadenza, ore, stato, colore
2. `openModal('project', project)` — se null è creazione, se ha dati è modifica
3. Dropdown stato: `x-model="p.status"` + `@change` per aggiornare label e classe
4. Pulsante "Avanza →": `advanceProjectStatus(p)` che incrementa l'indice dell'array stati

### Passo 6 — Rotta

1. Aggiungi `Route::resource('projects', ProjectController::class)` in web.php
2. Proteggi con middleware `auth`

## Files da creare/modificare

- `database/migrations/xxxx_create_projects_table.php`
- `app/Models/Project.php`
- `app/Http/Controllers/ProjectController.php`
- `resources/views/projects/index.blade.php`
- `routes/web.php`

## Status

[ ] Non iniziata
