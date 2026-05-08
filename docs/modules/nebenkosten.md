# Module: Nebenkostenabrechnungen

## Current State
NebenkostenabrechnungenController coordinates creation, preview, calculation, status changes, and deletion.

NebenkostenberechnungService already contains the core calculation logic.

## Strategic Direction: Beleg-OCR Integration

Nebenkostenabrechnungen should not be refactored in isolation.

The future workflow is:

1. Upload or scan invoice document
2. OCR extraction in newFeatures/Belege-OCR
3. Human-in-the-loop review of extracted invoice fields
4. Manual/assisted assignment to:
   - Objekt
   - Einheit, optional
   - Eingangsrechnung category
   - Nebenkosten category
   - Verteilerschluessel
5. Persist reviewed result as Eingangsrechnung
6. Use Eingangsrechnungen as source data for Nebenkostenabrechnung
7. Generate Nebenkostenabrechnung from reviewed and classified invoices

The OCR module is therefore an upstream data-entry and classification workflow for Nebenkostenabrechnungen.

## Target State
Controller:
- handles request/response
- delegates workflows to service

Service:
- creates Abrechnung from preview
- stores Positionen
- stores Einheiten
- calculates shares
- updates status

Model:
- persists Nebenkostenabrechnung records
- provides read queries

## Refactoring Priorities
1. Move creation workflow from controller to service.
2. Wrap multi-table creation in a transaction.
3. Add tests for calculation and creation.
4. Keep routes unchanged.

## Important Tables
- nebenkostenabrechnungen
- nebenkostenpositionen
- nk_einheiten
- nk_positionen_anteile
- nk_ergebnisse