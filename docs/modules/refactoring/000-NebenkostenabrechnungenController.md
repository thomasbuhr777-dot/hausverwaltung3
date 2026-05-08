# Analyze the existing newFeatures Belege-OCR app and the existing Nebenkosten/Eingangsrechnungen modules.

## Goal:
Create a technical integration plan for the workflow:

Beleg upload/OCR -> human review -> assignment to Objekt or Einheit -> NK category/key -> Eingangsrechnung -> Nebenkostenabrechnung.

## Do not change application code yet.

## Deliverables:
- docs/modules/belege-ocr.md
- update docs/modules/nebenkosten.md
- update docs/modules/eingangsrechnungen.md
- update docs/refactoring-roadmap.md

## The plan must identify:
- existing files/classes involved
- missing database fields/tables
- service boundaries
- controller responsibilities
- migration strategy
- risks
- first safe implementation task

## Rules:
- Preserve existing routes and behavior.
- Do not move files yet.
- Do not rewrite the OCR module yet.
- Treat OCR as upstream input for Eingangsrechnungen and Nebenkosten.