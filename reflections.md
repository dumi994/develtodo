# Reflections — DevelTodo

## 22/07/2026 — Setup iniziale

### Stato attuale

- Brief, ADR, prototipo HTML e specs completi
- Pronti per iniziare lo sviluppo Laravel + NativePHP

### Decisioni prese

- **PHP + Laravel + NativePHP**: unica codebase per desktop (Windows) e futuro mobile
- **SQLite**: database offline-first, zero configurazione
- **Blade + Tailwind + Alpine.js**: nessun build step JS, massima leggerezza
- **Ticket senza chat**: la chat è stata rimossa perché sconnessa da servizi esterni, non aggiungeva valore
- **Auto-updater**: NativePHP lo supporta nativamente, da attivare in fase di build

### Da tenere d'occhio

- NativePHP Desktop 2 è production-ready, ma la sezione mobile è ancora in sviluppo
- Laravel 11+ richiede PHP 8.3+, verificare versione installata
- Il passaggio da prototipo statico a viste Blade richiederà di ricreare l'interattività Alpine.js

### Cose da migliorare

- Niente chat ticket → i ticket sono solo informativi, forse in futuro un notes più ricco
- Time tracking non è essenziale ora, ma potrebbe diventarlo
- Search globale sarebbe comodo già dal MVP
