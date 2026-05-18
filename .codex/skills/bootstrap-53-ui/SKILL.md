---
name: bootstrap-53-ui
description: Verwende diesen Skill, wenn Bootstrap-5.3-Views, Formulare, Cards, Tabellen, Badges, Tags, Modals oder Light/Dark-Mode-kompatible UI-Komponenten erstellt werden.
---

# Bootstrap 5.3 UI Skill

## Ziel

Erstelle klare, wartbare Bootstrap-5.3-Oberflächen für die Hausverwaltungs-App.

## Regeln

- Light/Dark Mode muss funktionieren.
- `data-bs-theme` des Layouts respektieren.
- Keine festen Farben verwenden, wenn Bootstrap-Variablen ausreichen.
- Für kontrastkritische Texte explizit passende Textklassen setzen.
- Cards, Badges, Tabellen und Formulare semantisch sauber strukturieren.
- Icons vorzugsweise mit Bootstrap Icons.

## Dark/Light Mode

Bevorzugen:

- bg-body
- text-body
- text-body-secondary
- border
- border-secondary-subtle
- bg-body-tertiary
- bg-primary-subtle
- text-primary-emphasis
- border-primary-subtle

Bei dunklem Hintergrund explizit:

- text-white
- text-white-50

## Formulare

- Labels immer setzen.
- Hilfetexte mit form-text.
- Fehler mit invalid-feedback.
- Serverseitige Validation sichtbar machen.
- Pflichtfelder kennzeichnen.

## Tabellen

- table
- table-hover
- align-middle
- responsive Wrapper: table-responsive
- Empty-State statt leerer Tabelle

## Modals

- Eindeutige IDs.
- Formulare vollständig im Modal.
- Submit-Button klar benennen.
- Abbrechen-Button immer vorhanden.