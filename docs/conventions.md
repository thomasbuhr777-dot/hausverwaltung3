# Conventions

## PHP
- Use strict, readable PHP 8.2+ style.
- Add explicit return types to public methods where practical.
- Prefer typed properties where practical.
- Keep methods small and intention-revealing.
- Use German domain names consistently where already established.

## CodeIgniter 4
- Respect CI4 conventions for Controllers, Models, Views, Routes, Filters, Migrations, and Shield.
- Do not bypass CI4 services unnecessarily.
- Use named routes where they already exist.
- Keep Shield session routes intact.

## Controllers
- Controllers may instantiate Models in existing modules.
- New complex workflows should be delegated to Services.
- Do not add direct Query Builder insert/update/delete logic to controllers.
- Do not add calculation logic to controllers.

## Services
- Use Services for workflows and calculations.
- Services may use Models or DB connection.
- Prefer transactions when writing to multiple tables.
- Return structured arrays or DTOs consistently.

## Models
- Keep allowedFields complete and accurate.
- Use validationRules for persistence-level validation.
- Use soft deletes consistently where enabled.
- Keep nullable foreign key sanitation in Models when required by HTML forms.

## Views
- Views should not contain business logic.
- Views may contain presentation conditionals and formatting only.
- Avoid database access in views.

## Naming
Use existing German domain terminology:
- Objekt
- Einheit
- Adresse
- Mietvertrag
- Zahlung
- Eingangsrechnung
- Nebenkostenabrechnung

Use English only for technical concepts where already common:
- Controller
- Service
- Model
- Migration
- Test

## Refactoring
- Preserve behavior.
- Refactor in small steps.
- Do not rename routes without updating all references.
- Do not change database schema unless explicitly requested.
- Add comments only where they explain non-obvious domain behavior.