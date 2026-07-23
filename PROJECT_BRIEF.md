# 📋 **DevelTodo** — Project Brief

> **Applicazione desktop per web developer freelance** per gestire progetti, task, ticket clienti, preventivi e fatture.
> Un'unica codebase PHP (Laravel + NativePHP) per Windows, con futuro supporto mobile.

---

## 1. 🧠 Vision

Un tool **tutto-in-uno** pensato per web developer freelance che devono tenere traccia di:

- Cosa devono fare oggi (task con priorità e scadenze)
- Cosa chiedono i clienti (ticket con priorità)
- Quanto devono fatturare (preventivi e fatture con promemoria)
- Come stanno andando i progetti (dashboard con stato e scadenze)

**Principio chiave:** ogni azione deve essere chiara e immediata. Niente menu nascosti, niente flussi ambigui.

---

## 2. 🏗 Stack Tecnologico

| Layer               | Tecnologia                               |
| ------------------- | ---------------------------------------- |
| **Linguaggio**      | PHP 8.3+                                 |
| **Framework**       | Laravel 11+                              |
| **Desktop**         | NativePHP Desktop 2 (Electron)           |
| **Mobile (futuro)** | NativePHP Mobile 3                       |
| **Database**        | SQLite                                   |
| **Frontend**        | Laravel Blade + Tailwind CSS + Alpine.js |
| **Auth**            | Laravel Breeze                           |

---

## 3. 🎯 Funzionalità — Flussi Completi

### Modulo 1 — Progetti

Un progetto è un contenitore di task. Ogni task appartiene a un progetto (o è libero).

**Cosa vedo:**

- Griglia di cards, una per progetto
- Ogni card mostra: nome, cliente, stato, progresso %, task completati/totali, scadenza

**Cosa posso fare:**

- **Creare progetto:** clicco "+ Nuovo progetto" → modale con: nome, cliente, colore, data scadenza, ore stimate
- **Modificare:** clicco sulla card → si apre dettaglio con tutti i campi editabili
- **Cambiare stato:** dropdown con: `In arrivo → In corso → In revisione → Completato → Archiviato`
- **Eliminare:** icona cestino nel dettaglio

**Stati progetto:**

```
In arrivo    → grigio (non ancora iniziato)
In corso     → blu (in lavorazione)
In revisione → giallo (in attesa di feedback cliente)
Completato   → verde (consegnato)
Archiviato   → nascosto dalla dashboard (visibile in "Archivio")
```

---

### Modulo 2 — Task / Todo

Un task è un'azione singola. Può appartenere a un progetto o essere un task libero.

**Cosa vedo (vista Lista):**

- Elenco di task, ognuno con:
  - ☑ Checkbox (completato/non completato)
  - Titolo
  - Badge priorità (🔴 Alta / 🟡 Media / 🟢 Bassa)
  - Badge progetto (se associato)
  - Data scadenza (con countdown: "Oggi", "Domani", "Tra 3 giorni", "Scaduto!")
  - Subtask (pallini: verde=fatto, giallo=in corso, grigio=da fare)

**Cosa posso fare:**

- **Aggiungere task:** clicco "+ Nuovo task" → modale con: titolo, priorità, progetto (opzionale), data scadenza, subtask
- **Completare:** clicco la checkbox → task si spunta, va in fondo alla lista, si oscura
- **Filtrare:** tabs "Tutti | Alta priorità | In scadenza | Completati"
- **Vista Kanban:** 3 colonne (Da fare | In corso | Fatto) — trascino i task tra le colonne
- **Vista Calendario:** griglia mensile con pallini sui giorni con task in scadenza

**Checklist subtask:**

- Ogni task può avere una lista di subtask (es. "Preparare bozza", "Inviare al cliente", "Applicare revisioni")
- Ogni subtask ha un checkbox
- La barra di avanzamento del task si aggiorna in base ai subtask completati

---

### Modulo 3 — Ticket (assistenza clienti)

Un ticket è una richiesta di assistenza da un cliente. **Viene creato manualmente dall'utente** (niente import automatico).

**Cosa vedo:**

- Lista di ticket, ognuno con:
  - Badge priorità (🔴 Alta / 🟡 Media / 🟢 Bassa)
  - Oggetto
  - Nome cliente
  - Categoria (🐛 Bug / ✨ Feature / ❓ Supporto)
  - Stato (Aperto / In lavorazione / Chiuso)
  - Data ultimo aggiornamento

**Cosa posso fare:**

- **Aprire ticket:** clicco "+ Nuovo ticket" → modale con: cliente, oggetto, priorità, categoria, note
- **Visualizzare dettaglio:** clicco sul ticket → si espande con tutti i campi
- **Cambiare stato:** dropdown "Aperto → In lavorazione → Chiuso"
- **Ticket in sospeso:** se un ticket non viene aggiornato per 3+ giorni, appare un avviso

> **Nota:** la chat integrata è stata rimossa perché sconnessa da qualsiasi servizio esterno. I ticket sono solo informativi.

---

### Modulo 4 — Preventivi

Un preventivo è una proposta economica che invio a un potenziale cliente.

**Cosa vedo:**

- Griglia di cards preventivo, ognuna con:
  - Cliente
  - Descrizione (es. "Sito vetrina + blog")
  - Importo (€)
  - Stato (con badge colorato)
  - Scadenza validità
  - Icona PDF (se caricato)

**Stati preventivo:**

```
Bozza      → grigio (sto ancora lavorando)
Inviato    → giallo (in attesa di risposta)
Accettato  → verde (cliente ha detto sì → posso creare fattura)
Rifiutato  → rosso (cliente ha detto no)
Fatturato  → blu (è stata generata una fattura)
```

**Cosa posso fare:**

- **Creare:** clicco "+ Nuovo preventivo" → modale con: cliente, importo, descrizione, upload PDF, scadenza validità
- **Cambiare stato:** dropdown per avanzare lo stato
- **Da Accettato a Fatturato:** quando un preventivo è "Accettato", compare il pulsante "Crea fattura" → genera automaticamente una fattura con gli stessi dati
- **Scadenza:** se la data di scadenza validità è passata, il badge diventa rosso "Scaduto"

---

### Modulo 5 — Fatture

Una fattura è una richiesta di pagamento. Può nascere da un preventivo accettato o essere creata manualmente.

**Cosa vedo:**

- Tabella con: #fattura, cliente, importo, scadenza, stato
- Righe evidenziate in rosso se in ritardo

**Stati fattura:**

```
Da fare    → grigio (da preparare)
Inviata    → giallo (in attesa di pagamento)
Pagata     → verde (pagamento ricevuto)
Sollecito  → rosso (scaduta e non pagata)
```

**Cosa posso fare:**

- **Creare:** clicco "+ Nuova fattura" → modale con: cliente, importo, scadenza, note
- **Da preventivo:** se un preventivo è "Accettato", clicco "Crea fattura" → i dati si copiano automaticamente
- **Cambiare stato:** dropdown per avanzare lo stato
- **Promemoria:** se una fattura è "Inviata" e la scadenza è passata, scatta notifica "Fattura in ritardo!"

---

### Modulo 6 — Dashboard

**Cosa vedo:**

- **4 cards riepilogative:** Progetti attivi, Task aperti, Ticket aperti, Fatture in ritardo
- **Prossime scadenze:** lista di task/progetti in scadenza (oggi, domani, questa settimana)
- **Preventivi in attesa:** cards piccole con giorni di attesa
- **Grafico produttività:** task completati negli ultimi 7 giorni

---

## 🌐 Sistema Notifiche

Le notifiche arrivano come **dropdown nell'header** (e in futuro come notifiche native Windows).

**Quando scatta una notifica:**

- ⏰ Task in scadenza (24h prima)
- ⏰ Task scaduto (se passato)
- 💰 Fattura in ritardo
- 📄 Preventivo in attesa da > 7 giorni
- 🎫 Ticket non aggiornato da > 3 giorni
- 📋 Progetto in scadenza (7 giorni prima)

**Cosa posso fare:**

- Clicco sulla campanella → vedo l'elenco
- Clicco su una notifica → si segna come letta
- "Segna tutte lette" → pulisce tutto

---

## 4. 👥 Multi-utente

- Login con email + password
- Ogni utente vede solo i suoi dati
- Ruoli: admin (può vedere tutto) / user (solo i suoi dati)

---

## 5. 🎨 UI/UX — Linee guida

- **Design pulito e moderno** (ispirato a Linear, Notion, Todoist)
- **Sidebar** sempre visibile a sinistra
- **Header** con: titolo sezione, data, campanella notifiche, tema chiaro/scuro, avatar utente
- **Ogni azione** ha un pulsante chiaro e visibile
- **Feedback visivo** immediato (checkbox, badge, colori)
- **Tema scuro** attivabile con un click

---

## 6. 📅 Roadmap

### Fase 1 — MVP Desktop (3-4 settimane)

1. Setup Laravel + NativePHP + SQLite + Breeze
2. Modulo Progetti (CRUD + stati + progresso)
3. Modulo Task (CRUD + checkbox + priorità + scadenze + subtask)
4. Dashboard base (cards + scadenze imminenti)
5. Build nativa Windows (.exe)

### Fase 2 — Completo (3 settimane)

6. Modulo Ticket (senza chat — solo priorità, categoria, stato)
7. Modulo Preventivi (upload PDF + workflow stati)
8. Modulo Fatture (CRUD + promemoria + creazione da preventivo)
9. Sistema notifiche globale
10. Vista Kanban + Calendario
11. Tema chiaro/scuro

### Fase 3 — Rifinitura (1-2 settimane)

12. Auto-updater (NativePHP updater integrato — GitHub Releases)
13. Backup/restore SQLite
14. Esportazione report
15. Icona vassoio Windows
16. Auto-avvio

### Fase 4 — Mobile (futuro)

16. NativePHP Mobile 3
17. Sync dati desktop ↔ mobile
18. Notifiche push

---

## 7. 📦 Deliverables

1. App desktop Windows (.exe) — singola codebase PHP
2. Database SQLite locale (portabile, zero configurazione)
3. (Futuro) App iOS/Android

---

## 8. 💡 Extra (futuri)

- Timer pomodoro integrato
- Template progetti ricorrenti
- Collegamento GitHub/GitLab per importare issue
- Icona tray Windows con badge notifiche

---

## 9. 📐 Schema Database

```
users
├── id, name, email, password, theme

projects
├── id, user_id, name, description, client, color
├── status [in_arrivo|in_corso|in_revisione|completato|archiviato]
├── start_date, due_date, estimated_hours

tasks
├── id, project_id (nullable), user_id, title
├── priority [alta|media|bassa]
├── status [da_fare|in_corso|fatto]
├── due_date, completed_at, position (kanban)
├── subtasks (JSON) — [{title, done}]

tickets
├── id, user_id, client_name, subject
├── priority [alta|media|bassa]
├── category [bug|feature|supporto]
├── status [aperto|in_lavorazione|chiuso]
├── messages (JSON) — [{from, text, date}]

quotes
├── id, user_id, client, description, amount
├── status [bozza|inviato|accettato|rifiutato|fatturato]
├── file_path (PDF), expiry_date, created_at

invoices
├── id, user_id, quote_id (nullable), number
├── client, amount, status [da_fare|inviata|pagata|sollecito]
├── due_date, sent_at, paid_at

reminders
├── id, user_id, remindable_type, remindable_id
├── title, due_at, notified (bool)
```
