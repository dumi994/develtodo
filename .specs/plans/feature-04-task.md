# Feature: Task / Todo — Guida Implementazione

## Obiettivo

Task associabili a progetti con priorità, scadenze, checklist subtask e viste multiple (Lista, Kanban, Calendario).

## Dipendenze

Feature 01 (Layout), Feature 03 (Progetti), Modello Eloquent `Task`

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Controller + Migration

## Output atteso

- Vista Lista: checkbox per completamento, badge priorità, progetto, scadenza, subtask dots
- Filtri: Tutti, Alta priorità, In scadenza, Completati
- Vista Kanban: 3 colonne (Da fare, In corso, Fatto) con pulsanti "←" e "→" per spostare
- Vista Calendario: griglia mensile con pallini sui giorni con task in scadenza
- Subtask: lista interna con checkbox, progresso in pallini (verde/grigio)
- Modale creazione/modifica task

## Guida passo-passo

### Passo 1 — Database (Migration)

1. Crea migration: `php artisan make:migration create_tasks_table`
2. Campi: id, user_id, project_id (nullable), title, priority (enum), status (enum), due_date (nullable), completed_at (nullable), position (integer), kanbanCol (string default 'Da fare'), subtasks (json nullable)
3. Priority enum: 'alta', 'media', 'bassa'
4. Status enum: 'da_fare', 'in_corso', 'fatto'
5. `$casts = ['subtasks' => 'array', 'due_date' => 'date']`

### Passo 2 — Modello

1. `php artisan make:model Task`
2. `$fillable`, `$casts`
3. Relazioni: `belongsTo(User)`, `belongsTo(Project)`

### Passo 3 — Controller

1. `php artisan make:controller TaskController --resource`
2. index(): lista task con filtri (per priorità, scadenza, completati)
3. store(): crea task con subtask da textarea (split per newline)
4. update(): aggiorna campi, kanbanCol, done
5. destroy(): elimina

### Passo 4 — Vista

1. `resources/views/tasks/index.blade.php`
2. 3 tabs: Lista, Kanban, Calendario (Alpine `taskView`)
3. Lista: checkbox + `toggleTaskDone(t)` che aggiorna `done` e `kanbanCol`
4. Kanban: 3 colonne, pulsanti "← Da fare" / "In corso →" chiamano `moveKanban(t, -1/1)`
5. Calendario: calcola giorni del mese, evidenzia oggi, mostra pallini sui giorni con task

### Passo 5 — Subtask

1. Campo textarea nella modale: "uno per riga"
2. Alla creazione, converti in array `[{title, done:false}]`
3. Nell'elenco, mostra pallini colorati per ogni subtask
4. Contatore: "2/5" = completati/totali

### Passo 6 — Rotta

1. `Route::resource('tasks', TaskController::class)`

## Files da creare/modificare

- `database/migrations/xxxx_create_tasks_table.php`
- `app/Models/Task.php`
- `app/Http/Controllers/TaskController.php`
- `resources/views/tasks/index.blade.php`
- `routes/web.php`

## Status

[ ] Non iniziata
