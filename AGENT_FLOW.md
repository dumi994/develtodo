# Agent Development Workflow — DevelTodo

## Istruzioni Operative Generali

Questo flusso va eseguito in **loop continuo** per ogni singolo sotto-task della tabella di marcia (`.specs/plans/feature-*.md`).
L'agente **non deve passare al sotto-task successivo** finché quello corrente non è stato:

1. Sviluppato
2. Testato con esito positivo
3. Committato e fuso nel branch `dev`

---

## Step 1 — Isolamento della Feature (Branching)

Crea un branch dedicato partendo dall'ultimo stato stabile di `dev`:

```bash
git checkout dev
git pull origin dev
git checkout -b feature-<nome-sotto-task>
```

---

## Step 2 — Sviluppo

Implementa **esclusivamente** la singola feature richiesta dal sotto-task corrente.

| Vincolo                 | Regola                                                       |
| ----------------------- | ------------------------------------------------------------ |
| **Lingua del codice**   | Inglese (classi, ID, variabili, attributi)                   |
| **Lingua dei commenti** | Italiano                                                     |
| **Stack tecnologico**   | Laravel 11+ / PHP 8.2+ / Blade + Tailwind + Alpine.js / NativePHP (Electron) / SQLite |
| **Database**            | SQLite + migrazioni in `database/migrations/`                |
| **Test**                | Aggiungere/aggiornare i test Feature in `tests/Feature/` quando la feature ha logica backend |

---

## Step 3 — Test di Funzionamento

Prima di procedere al commit:

- [ ] Se c'è logica backend: `php artisan test` (oppure `vendor/bin/phpunit`) — rosso prima, verde dopo
- [ ] Se c'è UI: pagina **completamente interattiva** nel browser (clic, modali, tab)
- [ ] La console sviluppatori del browser riporta **zero errori**

Se anche uno solo dei controlli fallisce, tornare allo Step 2 e correggere.

---

## Step 4 — Staging e Commit Atomico

Selezionare **solo ed esclusivamente** i file che compongono la funzionalità appena completata.

Usare sempre `git add <file>` con il percorso esplicito. **Mai `git add .`**

```bash
# Verifica i file modificati
git status

# Aggiungi solo i file della feature corrente
git add app/Http/Controllers/<file>.php
git add resources/views/<file>.blade.php

# Commit atomico con Conventional Commits
git commit -m "<prefisso>: <descrizione in inglese della singola feature>"
```

Prefissi usati: `feat:` · `fix:` · `refactor:` · `docs:` · `style:` · `test:` · `chore:` · `build(ci):`

---

## Step 5 — Chiusura del Ciclo (Merge & Clean)

Una volta che la funzionalità è stabile, eseguire nell'ordine:

```bash
# 1. Torna sul branch principale
git checkout dev

# 2. Fonde la feature mantenendo la storia dei commit
git merge feature-<nome-sotto-task> --no-ff -m "Merge feature: <descrizione>"

# 3. Invia il codice su GitHub
git push origin dev

# 4. Elimina il branch locale della feature completata
git branch -d feature-<nome-sotto-task>
```

Dopo questo step, il ciclo ricomincia dallo **Step 1** con il sotto-task successivo.

---

## Note sul Desktop (NativePHP)

- Il packaging vive in `nativephp/electron/` (src, electron-builder.mjs, package.json)
- Prima di una build nativa servono gli asset compilati: `npm run build`
- Build desktop: `php artisan native:build win --no-interaction`
- La release multi-OS (Windows/macOS/Linux) è gestita dal workflow CI `.github/workflows/build-release.yml`

---

## Tabella di Marcia dei Sotto-Task

La roadmap aggiornata è in `.specs/plans/` — ogni file `feature-NN-*.md` definisce obiettivo, file in scope, step e criteri di accettazione. L'ordine di esecuzione è l'ordine numerico dei file.