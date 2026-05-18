---
name: git-workflow
description: Verwende diesen Skill, wenn Git, GitHub, Branches, Pull, Push, Rebase, Merge-Konflikte, Secret-Scanning, .gitignore oder Repository-Struktur betroffen sind.
---

# Git Workflow Skill

## Ziel

Hilf bei sicheren Git-Workflows für das Hausverwaltungsprojekt.

## Regeln

- Keine Secrets committen.
- Bei Secret-Scanning-Fehlern nicht nur Datei ändern, sondern Commit-Historie beachten.
- Vor reset/rebase/force-push Risiken erklären.
- Windows-PowerShell-Kommandos bevorzugen, wenn der Benutzer lokal arbeitet.
- Kurze, konkrete Befehlsblöcke liefern.
- Bei Konflikten Status prüfen lassen.

## Standarddiagnose

Bei Git-Problemen zuerst:

```bash
git status
git branch -vv
git remote -v
git log --oneline --decorate --graph -10