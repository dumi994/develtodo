# Feature: Ricerca Globale — Guida Implementazione

## Obiettivo

Cercare rapidamente task, progetti, ticket, preventivi e fatture da un'unica barra di ricerca nell'header.

## Dipendenze

Feature 01 (Layout) — per il posizionamento nell'header

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Controller

## Output atteso

- Barra di ricerca nell'header (icona lente o campo testuale)
- Digitando, mostra risultati raggruppati per tipo (Task, Progetti, Ticket, ecc.)
- Navigazione rapida: clic sul risultato → vai alla sezione corrispondente
- Filtro in tempo reale (Alpine.js) sui dati mock

## Guida passo-passo

### Passo 1 — HTML

1. Aggiungi input all'header: `<input type="search" x-model="searchQuery" placeholder="Cerca...">`
2. Dropdown risultati sotto l'input con `x-show="searchQuery.length > 1"`

### Passo 2 — Logica Alpine.js

1. `searchQuery` (stringa)
2. `searchResults` computed: filtra tasks, projects, tickets, quotes, invoices per match in title/name/client/subject
3. Mostra risultati raggruppati per tipo con emoji

### Passo 3 — Interazione

1. Clic sul risultato: `currentView = 'tasks'` + scrolla all'elemento (o aprine dettaglio)
2. Tasto ESC: chiudi dropdown, pulisci query
3. Click outside: chiudi dropdown

### Passo 4 — Backend (futuro)

1. Quando i dati verranno dal database, sostituisci filtro mock con chiamata AJAX
2. `Route::get('/search', [SearchController::class, 'search'])`
3. Cerca con `LIKE` su tutti i modelli

## Files da creare/modificare

- `resources/views/components/header.blade.php` (aggiungi input search)
- Logica Alpine in `resources/js/app.js`

## Status

[ ] Non iniziata
