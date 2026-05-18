# AGENTS.md

## Projektkontext

Dieses Repository ist eine CI4-Hausverwaltung.

Tech-Stack:
- PHP 8.2+
- CodeIgniter 4.7.x
- MariaDB/MySQL
- Bootstrap 5.3 mit Light/Dark Mode
- CodeIgniter Shield
- Windows/XAMPP lokal, teilweise Docker/Ubuntu

## Arbeitsweise

- Bei Refactorings immer vollständige Dateien liefern, keine losen Teil-Snippets.
- Bestehende Architektur respektieren.
- Keine Änderungen an vendor/, writable/cache/, writable/logs/ oder .env.
- Keine Secrets, API-Keys oder Zugangsdaten committen.
- Vor riskanten Änderungen erst betroffene Dateien analysieren.
- Bei Datenbankänderungen Migrationen bevorzugen.
- Views müssen Bootstrap-5.3-kompatibel sein.
- UI-Komponenten müssen Light/Dark Mode unterstützen.

## CodeIgniter-Konventionen

- Controller unter app/Controllers.
- Models unter app/Models.
- Views unter app/Views.
- Migrationen unter app/Database/Migrations.
- Seeder unter app/Database/Seeds.
- Tabellen und Felder in snake_case.
- Controller in PascalCase.
- Methoden in camelCase.
- Models nutzen allowedFields, validationRules, useTimestamps und bei Bedarf useSoftDeletes.

## Antwortstil

- Deutsch.
- Praktisch, direkt, entwicklerorientiert.
- Bei Fehleranalyse: Ursache, konkrete Fundstelle, Lösung.
- Bei Code: vollständige Dateien mit Pfadüberschrift.