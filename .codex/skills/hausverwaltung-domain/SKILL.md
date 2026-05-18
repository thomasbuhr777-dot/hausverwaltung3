---
name: hausverwaltung-domain
description: Verwende diesen Skill, wenn fachliche Entitäten der Hausverwaltung betroffen sind, insbesondere Adressen, Objekte, Einheiten, Mietverträge, Mieter, Vermieter, Eigentümer, Ausstattung, Bankdaten oder Abrechnungsvorbereitung.
---

# Hausverwaltung Domain Skill

## Ziel

Berücksichtige die fachliche Struktur der Hausverwaltungsanwendung.

## Zentrale Entitäten

- Adressen
- Objekte
- Einheiten
- Mietverträge
- Mieter
- Vermieter
- Eigentümer
- Ausstattungsmerkmale
- Bankdaten
- Belege
- Abrechnungsrelevante Stammdaten

## Adressen

Die Tabelle `adressen` ist ein allgemeines Adressbuch.

Typische Felder:

- id
- adress_art
- anrede
- titel
- firmenname
- vorname
- nachname
- firma
- strasse
- plz
- ort
- land
- telefon1
- telefon2
- email
- steuer_id
- bankverbindung
- notizen
- created_at
- updated_at
- deleted_at
- created_by
- updated_by

## Wichtige Regeln

- `firma` und `firmenname` nicht ungeprüft gleichsetzen.
- Adressen können natürliche Personen oder Firmen sein.
- Eine Adresse kann fachlich verschiedene Rollen haben.
- Rollen nicht vorschnell als separate Tabellen modellieren.
- Bei Ausstattungsmerkmalen Kategorien und Tags berücksichtigen.
- Mietvertragsdaten müssen später abrechnungsfähig bleiben.

## UI-Fachlichkeit

- Fachliche Bezeichnungen deutsch.
- Keine unnötigen Anglizismen in der Oberfläche.
- Formulare für Vermietung/Verwaltung alltagstauglich halten.