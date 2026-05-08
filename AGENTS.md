# Agent Instructions for hausverwaltung3

This is a CodeIgniter 4 real-estate/property-management application.

## Stack
- PHP 8.2+
- CodeIgniter 4.7+
- CodeIgniter Shield
- PHPUnit

## Domain
The application manages:
- Objekte
- Einheiten
- Adressen
- Mietverträge
- Zahlungen
- Eingangsrechnungen
- Nebenkostenabrechnungen
- Lookup/settings tables
- User profile

## Mandatory Rules
- Follow docs/architecture.md
- Follow docs/conventions.md
- Keep changes small and scoped
- Preserve existing behavior unless the task explicitly changes it
- Do not introduce unrelated refactorings
- Do not rewrite complete modules unless explicitly requested

## Current Architecture Direction
The project is being migrated gradually toward:

Controller -> Service -> Repository/Model -> Database

Models may still contain query methods during transition.
New complex business logic should go into Services.
Direct DB writes in controllers should be moved into Services or Models during refactoring.

## Security
- Shield session authentication is used.
- Keep protected routes behind the session filter.
- Never weaken authentication or authorization.
- Validate user input before persistence.

## Testing
- Add or update tests for business logic.
- Prefer service-level tests for calculations and workflows.
- Run composer test when possible.