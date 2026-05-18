---
name: ci4-migrations
description: Verwende diesen Skill, wenn CodeIgniter-4-Migrationen, Seeder, Datenbanktabellen, Indizes, Foreign Keys oder MySQL/MariaDB-Schemaänderungen erstellt oder korrigiert werden.
---

# CodeIgniter 4 Migration Skill

## Ziel

Erstelle sichere, wiederholbare Migrationen und Seeder für MariaDB/MySQL in CodeIgniter 4.

## Regeln

- Migrationen müssen idempotent gedacht sein.
- Vor dem Hinzufügen von Spalten prüfen, ob sie existieren, wenn die Migration auf bestehende Tabellen zielt.
- Sinnvolle Feldtypen verwenden.
- created_at, updated_at, deleted_at als nullable datetime.
- created_by und updated_by als int unsigned nullable.
- Indizes eindeutig benennen.
- Duplicate-Key-Probleme vermeiden.
- Down-Methode sauber implementieren, sofern ungefährlich.

## Standardfelder

Für Stammdatentabellen bevorzugen:

- id INT UNSIGNED AUTO_INCREMENT
- created_at DATETIME NULL
- updated_at DATETIME NULL
- deleted_at DATETIME NULL
- created_by INT UNSIGNED NULL
- updated_by INT UNSIGNED NULL

## MySQL/MariaDB-Konventionen

- Tabellen: snake_case plural oder projektüblich.
- Spalten: snake_case.
- Slugs: VARCHAR(120) oder VARCHAR(160), unique nur wenn fachlich eindeutig.
- Boolean: TINYINT(1) bzw. CodeIgniter boolean.
- Geldbeträge: DECIMAL(10,2), nicht FLOAT.
- Flächen: DECIMAL(10,2).
- Sortierung: INT UNSIGNED DEFAULT 0.

## Seeder-Regeln

- Seeder sollen vorhandene Datensätze anhand slug oder eindeutigem Schlüssel erkennen.
- Keine blinden Mehrfacheinfügungen.
- update oder insert je nach Existenz.