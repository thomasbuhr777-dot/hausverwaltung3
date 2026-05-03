### CI4-Integration:

1. **Migration & Datenbank**: Ein Migration-File erstellt die Tabelle belege mit allen nötigen Feldern wie vendor, invoice_number, net_amount, total_amount, iban, etc.
2. **BelegModel**: Enthält die Felder-Konfiguration (allowedFields) und eine Beispiel-Methode zur IBAN-Validierung.
3. **BelegController**:**Lokale Speicherung**: Die hochgeladene Datei wird mit getRandomName() umbenannt und im Ordner writable/uploads/belege/ gespeichert.**KI-Extraktion**: Nutzt curl, um Gemini direkt aus PHP aufzurufen. Der Prompt wurde so angepasst, dass **IBAN**, **Nettopreis** und **Rechnungsnummer** gezielt extrahiert werden.
4. **Human-in-the-Loop View**:Verwendet **Bootstrap 5** für ein sauberes Interface.Präsentiert die extrahierten Daten in einem interaktiven Formular.Speichert den lokalen Dateinamen als hidden input, damit die Datenbank-Referenz stimmt.
5. **Routes**: Klare Endpunkte für den Upload-Workflow.

### Integration lokal:

1. Dateien in die CI4-Ordnerstruktur kopieren.
2. GEMINI_API_KEY im BelegController.
3. php spark migrate
4. Ordner writable/uploads/belege/ muss beschreibbar sein.