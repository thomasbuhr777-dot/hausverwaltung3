<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Hauptseite für den Upload
$routes->get('belege', 'BelegController::index');

// Hilfe zur Modell-Suche (temporär)
$routes->get('belege/debug-models', 'BelegController::listModels');

// Verarbeitet den Upload und führt die Analyse aus
$routes->post('belege/analyze', 'BelegController::analyze');

// Speichert die manuell kontrollierten Daten
$routes->post('belege/store', 'BelegController::store');
