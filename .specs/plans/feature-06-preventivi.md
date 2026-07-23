# Feature: Preventivi — Guida Implementazione

## Obiettivo

Preventivi con upload PDF, stato workflow e scadenza validità. Se accettato → genera fattura.

## Dipendenze

Feature 01 (Layout), Feature 07 (Fatture), Modello Eloquent `Quote`

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Controller + Migration + File Storage

## Output atteso

- Griglia cards preventivo: cliente, descrizione, importo, stato, scadenza, icona PDF
- Stati: Bozza → Inviato → Accettato → Rifiutato → Fatturato
- Pulsante "Crea fattura" visibile solo se stato = "Accettato"
- Upload PDF con drag & drop
- Badge "Scaduto" se expiry_date passata
- Modale creazione/modifica con upload file

## Guida passo-passo

### Passo 1 — Database

1. Migration: id, user_id, client, description, amount (decimal), status (enum), file_path (nullable), expiry_date (nullable)
2. Status: 'bozza', 'inviato', 'accettato', 'rifiutato', 'fatturato'

### Passo 2 — Modello

1. `php artisan make:model Quote`
2. `$fillable`, `$casts`

### Passo 3 — Controller

1. `php artisan make:controller QuoteController --resource`
2. store(): gestisci upload PDF con `$request->file('file')->store('quotes')`
3. update(): gestisci cambio stato, se 'accettato' mostra pulsante "Crea fattura"

### Passo 4 — Vista

1. `resources/views/quotes/index.blade.php`
2. Cards con dropdown cambio stato
3. Pulsante "Crea fattura da preventivo" (solo se accettato)
4. `createInvoiceFromQuote(q)`: copia cliente, importo in nuova fattura, cambia stato a 'fatturato'

### Passo 5 — Rotta

1. `Route::resource('quotes', QuoteController::class)`

## Files da creare/modificare

- `database/migrations/xxxx_create_quotes_table.php`
- `app/Models/Quote.php`
- `app/Http/Controllers/QuoteController.php`
- `resources/views/quotes/index.blade.php`
- `routes/web.php`

## Status

[ ] Non iniziata
