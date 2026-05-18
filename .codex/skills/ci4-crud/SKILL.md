---
name: ci4-crud
description: Verwende diesen Skill, wenn CRUD-Module, Controller, Models, Views, Formulare, Modals, Validierung oder REST-/Resource-Routen für CodeIgniter 4 erstellt oder refaktoriert werden sollen.
---

# CodeIgniter 4 CRUD Skill

## Ziel

Erstelle robuste CRUD-Module für CodeIgniter 4 im Stil dieses Hausverwaltungsprojekts.

## Regeln

- Immer vollständige Dateien liefern.
- Keine isolierten Code-Fragmente, außer der Benutzer fragt ausdrücklich danach.
- Bestehende Routen, Namespaces und Layouts berücksichtigen.
- CSRF berücksichtigen.
- Validation über Model oder Controller sauber abbilden.
- Flashdata für Erfolgs- und Fehlermeldungen verwenden.
- Redirect nach erfolgreichem POST/PUT/DELETE verwenden.
- Keine SPA-Komplexität, sofern nicht ausdrücklich verlangt.
- Einfache serverseitige Views bevorzugen.

## Standardstruktur

Für ein neues Modul liefere nach Möglichkeit:

- Controller
- Model
- Migration
- Seeder, falls Stammdaten
- index.php View
- create/edit Modal oder eigene create/edit Views
- passende Routen

## Model-Konvention

Models sollen enthalten:

- protected $table
- protected $primaryKey = 'id'
- protected $useAutoIncrement = true
- protected $returnType = 'array'
- protected $useSoftDeletes bei löschbaren Stammdaten
- protected $allowedFields
- protected $useTimestamps = true
- protected $createdField = 'created_at'
- protected $updatedField = 'updated_at'
- protected $deletedField = 'deleted_at'
- protected $validationRules
- protected $validationMessages

## Controller-Konvention

Controller sollen:

- request validieren
- Model nutzen, nicht direkt SQL, außer begründet
- try/catch nur dort verwenden, wo sinnvoll
- session()->setFlashdata() verwenden
- redirect()->back()->withInput() bei Fehlern verwenden
- benutzerfreundliche Fehlermeldungen liefern

## View-Konvention

Views sollen:

- Bootstrap 5.3 verwenden
- Dark/Light Mode unterstützen
- esc() für Ausgabe verwenden
- old() für Formularwerte verwenden
- klare Empty-States enthalten
- keine unnötige JavaScript-Komplexität einführen

## Häufige Projektfallen

- Nicht `getElementByID`, sondern `getElementById`.
- Bei Bootstrap-Modals sicherstellen, dass IDs eindeutig sind.
- Keine DataTables verwenden, wenn einfache HTML-Tabellen ausreichen.
- Feld `firma` und `firmenname` nicht verwechseln.