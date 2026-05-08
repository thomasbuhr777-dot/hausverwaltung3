# Refactoring Roadmap

## Strategic Refactoring Order

Do not refactor Nebenkostenabrechnungen as an isolated module first.

Preferred order:

1. Document Belege-OCR workflow
2. Define reviewed invoice data model
3. Define mapping from OCR result to Eingangsrechnung
4. Add assignment fields:
   - objekt_id
   - einheit_id
   - nk_kategorie
   - verteilerschluessel
   - human_review_status
5. Integrate reviewed invoices into Eingangsrechnungen
6. Refactor Nebenkostenabrechnungen to consume classified Eingangsrechnungen
7. Refactor controllers/services after workflow boundaries are stable