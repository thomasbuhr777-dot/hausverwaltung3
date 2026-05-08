# Module: Objekte

## Current State
ObjekteController handles CRUD for properties.
ObjektModel contains persistence config, validation, soft deletes, custom query methods, and nullable FK sanitation.

## Target State
Keep ObjektModel as main persistence boundary for now.
Do not introduce repository unless object queries grow significantly.

## Good Existing Patterns
- explicit return types
- notFound helper with HTTP 404
- typed model property
- model validation
- nullable FK sanitation
- soft deletes

## Refactoring Priorities
1. Keep controller thin.
2. Extract bezeichnung generation into service only if reused elsewhere.
3. Preserve existing routes.
4. Add tests around ObjektModel validation.