# Feature: Layout & Sidebar — Guida Implementazione

## Obiettivo

Creare la struttura base dell'app: sidebar di navigazione, header e area contenuto.

## Dipendenze

Nessuna — è il primo componente da realizzare.

## Stack

Laravel Blade + Tailwind CSS + Alpine.js

## Output atteso

- Sidebar fissa a sinistra (220px aperta, 60px chiusa)
- 6 voci di navigazione: Dashboard, Progetti, Task, Ticket, Preventivi, Fatture
- Header con: titolo sezione, data corrente, campanella notifiche, toggle tema, avatar utente
- Area contenuto principale che cambia in base alla voce selezionata
- Tema chiaro/scuro persistente (localStorage)
- Sidebar richiudibile con pulsante in basso

## Guida passo-passo

### Passo 1 — Struttura HTML

1. Crea un layout Blade `resources/views/layouts/app.blade.php`
2. Usa `<div class="flex h-screen overflow-hidden">` per il layout principale
3. Aside per la sidebar, `<div class="flex-1 flex flex-col min-w-0">` per il contenuto
4. Header dentro il contenuto, main sotto

### Passo 2 — Sidebar

1. Crea un array Alpine.js `navItems` con: id, label, icona (emoji)
2. Cicla con `x-for` per generare i pulsanti
3. Collega `@click="currentView = item.id"` per cambiare vista
4. Usa `x-show="sidebarOpen"` per mostrare/nascondere le label
5. Pulsante in fondo con hamburger per toggle larghezza

### Passo 3 — Header

1. Mostra `currentViewLabel` (mappa id → label italiana)
2. Mostra `currentDate` formattata in italiano
3. Campanella notifiche con badge `unreadNotifs`
4. Toggle tema: `@click="darkMode = !darkMode; localStorage.setItem(...)"`
5. Avatar utente con iniziale

### Passo 4 — Navigazione viste

1. Usa `x-show="currentView === 'dashboard'"` (e così via) per ogni sezione
2. Aggiungi classe `x-cloak` e animazione `fade-in`

### Passo 5 — Tema scuro

1. Classe `dark` sull'html tramite Alpine: `:class="{'dark': darkMode}"`
2. Tailwind: `dark:bg-gray-900` su body, `dark:bg-gray-800` su card/sidebar
3. localStorage: salva e carica il tema all'avvio

### Passo 6 — Eventuali pulsanti azione

1. Ogni sezione deve avere pulsante principale (es. "+ Nuovo progetto") che apre modale
2. Il pulsante modale chiama `openModal('tipo', null)`

## Files da creare/modificare

- `resources/views/layouts/app.blade.php` — layout principale
- `resources/views/components/sidebar.blade.php` — sidebar
- `resources/views/components/header.blade.php` — header
- `resources/js/app.js` — Alpine.js data

## Status

[ ] Non iniziata
