# Task 001 - Belege OCR Analysis

## Goal
Understand the current OCR workflow and define integration points.

## Background
The OCR workflow shall become the upstream process for:
- Eingangsrechnungen
- Nebenkostenabrechnungen

## Current Situation
Describe current implementation.

## Problems
- Missing classification workflow
- Missing review state
- Missing NK assignment structure

## Desired Architecture
Describe target workflow.

## Required Changes
- Database
- Services
- Controllers
- Views
- OCR pipeline

## Constraints
- Preserve current behavior
- No route changes
- No big-bang rewrite

## Deliverables
- Updated documentation
- Proposed migrations
- Service boundaries

## Risks
- Existing invoice workflow coupling
- Incomplete OCR extraction
- User workflow complexity

## Out of Scope
- Full UI redesign
- AI model replacement