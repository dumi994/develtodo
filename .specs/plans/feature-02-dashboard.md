# Feature: Dashboard — Guida Implementazione

## Obiettivo

Vista riepilogativa con cards statistiche, scadenze imminenti, preventivi in attesa e grafico produttività.

## Dipendenze

Feature 01 (Layout) — prerequisite

## Stack

Laravel Blade + Tailwind CSS + Alpine.js + Chart.js (via CDN)

## Output atteso

- 4 cards riepilogative: Progetti attivi, Task aperti, Ticket aperti, Fatture in ritardo
- Lista "Prossime scadenze" con task/progetti in scadenza (oggi, domani, questa settimana)
- Sezione "Preventivi in attesa" con giorni di attesa
- Grafico a barre "Task completati (ultimi 7 giorni)" con Chart.js
- Dati mock statici in Alpine.js

## Guida passo-passo

### Passo 1 — Cards statistiche

1. Crea griglia `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4`
2. Ogni card: div con bg-white, rounded-xl, p-4, border
3. Layout: label (testo grigio), numero (testo grande bold), icona (sfondo colorato), trend (testo verde/rosso)
4. Dati mock: `stats` con activeProjects, newProjects, openTasks, dueToday, openTickets, highPriorityTickets, overdueInvoices, totalUnpaid

### Passo 2 — Prossime scadenze

1. Griglia 2/3 + 1/3
2. Colonna grande: titolo "📅 Prossime scadenze"
3. Lista con `x-for="item in upcomingDeadlines"`
4. Ogni riga: pallino colorato, titolo, progetto, giorni rimanenti
5. Colore rosso + animazione pulse se urgente (oggi/domani)

### Passo 3 — Preventivi in attesa

1. Colonna piccola: titolo "📄 Preventivi in attesa"
2. Cards piccole con cliente, importo, stato, giorni di attesa
3. Badge rosso se in scadenza, giallo se in attesa

### Passo 4 — Grafico Chart.js

1. Canvas: `<canvas id="prodChart">`
2. Inizializza nel `init()` o dopo il DOM
3. Bar chart con 7 giorni, dati mock
4. Tema scuro: cambia colore testo/bordi se darkMode

### Passo 5 — Refresh dati

1. I dati mock sono statici nell'oggetto Alpine
2. In futuro verranno sostituiti con chiamate AJAX/API

## Files da creare/modificare

- `resources/views/dashboard.blade.php` — vista dashboard
- Dati mock in `resources/js/app.js` (oggetto `stats`)

## Status

[ ] Non iniziata
