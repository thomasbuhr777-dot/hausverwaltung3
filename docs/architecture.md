# Architecture

## Application Type
hausverwaltung3 is a monolithic CodeIgniter 4 web application for property management.

## Main Modules
- Dashboard
- Adressen
- Objekte
- Einheiten
- Mietverträge
- Zahlungen
- Eingangsrechnungen
- Nebenkostenabrechnungen
- Settings/Lookup tables
- Profile

## Target Layering

Controller -> Service -> Repository/Model -> Database

This is a target architecture. The existing project may still contain legacy patterns.

## Controllers
Controllers handle HTTP concerns only:
- read request data
- call services or models
- choose view/redirect/json response
- handle flash messages
- map not-found cases to HTTP 404 or redirects

Controllers must avoid:
- complex business rules
- direct query builder writes
- calculation logic
- large transactional workflows

## Services
Services contain business workflows:
- calculations
- status transitions
- creation workflows involving multiple tables
- validation of business invariants
- transaction boundaries

Example:
NebenkostenberechnungService owns cost-allocation logic.

## Models
Models remain valid CI4 persistence classes:
- table configuration
- allowedFields
- validation rules
- timestamps
- soft deletes
- simple query methods

During migration, existing custom query methods may remain in Models.
Do not move all Model logic at once.

## Repositories
Repositories are optional for now.
Introduce them only when a module becomes too complex or when data access needs a clear boundary.

## Domain Rules
- Objekte represent managed buildings/properties.
- Einheiten belong to Objekte.
- Mietverträge connect tenants to Einheiten.
- Zahlungen belong to Mietverträge.
- Eingangsrechnungen may belong to Objekte or Einheiten.
- Nebenkostenabrechnungen aggregate costs and distribute them to units.

## Refactoring Strategy
Refactor one module at a time.
Never mix architectural migration with unrelated feature work.