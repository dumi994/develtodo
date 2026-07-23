# Feature: Fatture — Guida Implementazione

## Obiettivo

Fatture con promemoria, stato workflow, creazione manuale o da preventivo accettato.

## Dipendenze

Feature 01 (Layout), Feature 06 (Preventivi), Modello Eloquent `Invoice`

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Controller + Migration

## Output atteso

- Tabella fatture: #fattura, cliente, importo, scadenza, stato
- Righe evidenziate in rosso se in ritardo (overdue)
- Stati: Da fare → Inviata → Pagata → Sollecito
- Dropdown cambio stato
- Pulsante "+ Nuova fattura" → modale con cliente, importo, scadenza
- Creazione automatica da preventivo accettato
- Promemoria visivo per fatture non pagate oltre scadenza

## Guida passo-passo

### Passo 1 — Database

1. Migration: id, user_id, quote_id (nullable), number (string), client, amount (decimal), status (enum), due_date (date), sent_at (nullable), paid_at (nullable)
2. Status: 'da_fare', 'inviata', 'pagata', 'sollecito'

### Passo 2 — Modello

1. `php artisan make:model Invoice`
2. `$fillable`, `$casts`
3. `belongsTo(Quote)` nullable

### Passo 3 — Controller

1. `php artisan make:controller InvoiceController --resource`
2. store(): genera numero fattura progressivo
3. Metodo helper: `isOverdue()` se status != 'pagata' e due_date passata

### Passo 4 — Vista

1. `resources/views/invoices/index.blade.php`
2. Tabella HTML con righe colorate se overdue
3. Dropdown cambio stato inline
4. Pulsante "+ Nuova fattura" → modale

### Passo 5 — Rotta

1. `Route::resource('invoices', InvoiceController::class)`

## Files da creare/modificare

- `database/migrations/xxxx_create_invoices_table.php`
- `app/Models/Invoice.php`
- `app/Http/Controllers/InvoiceController.php`
- `resources/views/invoices/index.blade.php`
- `routes/web.php`

## Status

[ ] Non iniziata
