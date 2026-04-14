<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Immobilienverwaltung Routes
 * Alle Routen sind Shield-geschützt (auth-Gruppe)
 */

/** @var RouteCollection $routes */

// Shield-Routen (Login, Logout, Register, Magic-Link, etc.)
// Dies registriert automatisch alle von Shield benötigten Routen korrekt.
service('auth')->routes($routes);

// Geschützte Routen
$routes->group('', ['filter' => 'session'], function ($routes) {

    // Dashboard
    $routes->get('/',          'DashboardController::index', ['as' => 'dashboard']);
    $routes->get('dashboard',  'DashboardController::index');



/****************************************************************************
 * LookupController - Für Audittables *
*****************************************************************************/
$routes->group('settings', ['filter' => 'session'], function ($routes) {
    $routes->get('lookup/(:segment)', 'Settings\LookupController::index/$1');
    $routes->get('lookup/(:segment)/create', 'Settings\LookupController::create/$1');
    $routes->post('lookup/(:segment)/store', 'Settings\LookupController::store/$1');
    $routes->get('lookup/(:segment)/edit/(:num)', 'Settings\LookupController::edit/$1/$2');
    $routes->post('lookup/(:segment)/update/(:num)', 'Settings\LookupController::update/$1/$2');
    $routes->post('lookup/(:segment)/toggle/(:num)', 'Settings\LookupController::toggle/$1/$2');
    $routes->post('lookup/(:segment)/sort', 'Settings\LookupController::sort/$1');
});

    // ----------------------------------------------------------------
    // Nebenkostenabrechnungen
    // ----------------------------------------------------------------
    $routes->group('nebenkosten', function ($routes) {
        $routes->get('/',                       'NebenkostenabrechnungenController::index',            ['as' => 'nebenkosten.index']);
        $routes->get('neu',                     'NebenkostenabrechnungenController::new',              ['as' => 'nebenkosten.new']);
        $routes->get('vorschau',                'NebenkostenabrechnungenController::vorschau',         ['as' => 'nebenkosten.vorschau']);
        $routes->post('/',                      'NebenkostenabrechnungenController::create',           ['as' => 'nebenkosten.create']);
        $routes->get('(:num)',                  'NebenkostenabrechnungenController::show/$1',          ['as' => 'nebenkosten.show']);
        $routes->post('(:num)/berechnen',       'NebenkostenabrechnungenController::berechnen/$1',     ['as' => 'nebenkosten.berechnen']);
        $routes->post('(:num)/status',          'NebenkostenabrechnungenController::statusAendern/$1', ['as' => 'nebenkosten.status']);
        $routes->post('(:num)/loeschen',        'NebenkostenabrechnungenController::delete/$1',        ['as' => 'nebenkosten.delete']);
        $routes->get('einheit/(:num)',          'NebenkostenabrechnungenController::einheitAbrechnung/$1', ['as' => 'nebenkosten.einheit']);
    });

    // ----------------------------------------------------------------
    // Adressen (CRUD + Typeahead-API + Schnellanlage)
    // ----------------------------------------------------------------
    $routes->group('adressen', function ($routes) {
        $routes->get('/',                'AdressenController::index',        ['as' => 'adressen.index']);
        $routes->post('/',               'AdressenController::create',       ['as' => 'adressen.create']);
        $routes->get('suche',            'AdressenController::suche',        ['as' => 'adressen.suche']);
        $routes->post('schnell',         'AdressenController::schnellanlage', ['as' => 'adressen.schnell']);
        $routes->get('(:num)',           'AdressenController::show/$1',      ['as' => 'adressen.show']);
        $routes->get('(:num)/bearbeiten','AdressenController::edit/$1',      ['as' => 'adressen.edit']);
        $routes->post('(:num)',          'AdressenController::update/$1',    ['as' => 'adressen.update']);
        $routes->post('(:num)/loeschen', 'AdressenController::delete/$1',    ['as' => 'adressen.delete']);
    });

    // ----------------------------------------------------------------
    // Objekte
    // ----------------------------------------------------------------
    $routes->group('objekte', function ($routes) {
        $routes->get('/',          'ObjekteController::index',         ['as' => 'objekte.index']);
        $routes->get('neu',        'ObjekteController::new',           ['as' => 'objekte.new']);
        $routes->post('/',         'ObjekteController::create',        ['as' => 'objekte.create']);
        $routes->get('(:num)',     'ObjekteController::show/$1',       ['as' => 'objekte.show']);
        $routes->get('(:num)/bearbeiten', 'ObjekteController::edit/$1', ['as' => 'objekte.edit']);
        $routes->post('(:num)',    'ObjekteController::update/$1',     ['as' => 'objekte.update']);
        $routes->post('(:num)/loeschen', 'ObjekteController::delete/$1', ['as' => 'objekte.delete']);
    });

    // ----------------------------------------------------------------
    // Einheiten
    // ----------------------------------------------------------------
    $routes->group('einheiten', function ($routes) {
        $routes->get('/',          'EinheitenController::index',        ['as' => 'einheiten.index']);
        $routes->get('neu',        'EinheitenController::new',          ['as' => 'einheiten.new']);
        $routes->post('/',         'EinheitenController::create',       ['as' => 'einheiten.create']);
        $routes->get('(:num)',     'EinheitenController::show/$1',      ['as' => 'einheiten.show']);
        $routes->get('(:num)/bearbeiten', 'EinheitenController::edit/$1', ['as' => 'einheiten.edit']);
        $routes->post('(:num)',    'EinheitenController::update/$1',    ['as' => 'einheiten.update']);
        $routes->post('(:num)/loeschen', 'EinheitenController::delete/$1', ['as' => 'einheiten.delete']);
    });

    // ----------------------------------------------------------------
    // Mietverträge
    // ----------------------------------------------------------------
    $routes->group('mietvertraege', function ($routes) {
        $routes->get('/',          'MietvertraegeController::index',    ['as' => 'mietvertraege.index']);
        $routes->get('neu',        'MietvertraegeController::new',      ['as' => 'mietvertraege.new']);
        $routes->post('/',         'MietvertraegeController::create',   ['as' => 'mietvertraege.create']);
        $routes->get('(:num)',     'MietvertraegeController::show/$1',  ['as' => 'mietvertraege.show']);
        $routes->get('(:num)/bearbeiten', 'MietvertraegeController::edit/$1', ['as' => 'mietvertraege.edit']);
        $routes->post('(:num)',    'MietvertraegeController::update/$1', ['as' => 'mietvertraege.update']);
        $routes->post('(:num)/kuendigen', 'MietvertraegeController::kuendigen/$1', ['as' => 'mietvertraege.kuendigen']);
        $routes->post('(:num)/loeschen', 'MietvertraegeController::delete/$1', ['as' => 'mietvertraege.delete']);
    });

    // ----------------------------------------------------------------
    // Zahlungen
    // ----------------------------------------------------------------
    $routes->group('zahlungen', function ($routes) {
        $routes->get('/',          'ZahlungenController::index',        ['as' => 'zahlungen.index']);
        $routes->get('neu',        'ZahlungenController::new',          ['as' => 'zahlungen.new']);
        $routes->post('/',         'ZahlungenController::create',       ['as' => 'zahlungen.create']);
        $routes->get('(:num)/bearbeiten', 'ZahlungenController::edit/$1', ['as' => 'zahlungen.edit']);
        $routes->post('(:num)',    'ZahlungenController::update/$1',    ['as' => 'zahlungen.update']);
        $routes->post('(:num)/bezahlt', 'ZahlungenController::alsBezahlt/$1', ['as' => 'zahlungen.bezahlt']);
        $routes->post('(:num)/loeschen', 'ZahlungenController::delete/$1', ['as' => 'zahlungen.delete']);
    });

    // ----------------------------------------------------------------
    // Eingangsrechnungen
    // ----------------------------------------------------------------
    $routes->group('eingangsrechnungen', function ($routes) {
        $routes->get('/',          'EingangsrechnungenController::index',   ['as' => 'eingangsrechnungen.index']);
        $routes->get('neu',        'EingangsrechnungenController::new',     ['as' => 'eingangsrechnungen.new']);
        $routes->post('/',         'EingangsrechnungenController::create',  ['as' => 'eingangsrechnungen.create']);
        $routes->get('(:num)',     'EingangsrechnungenController::show/$1', ['as' => 'eingangsrechnungen.show']);
        $routes->get('(:num)/bearbeiten', 'EingangsrechnungenController::edit/$1', ['as' => 'eingangsrechnungen.edit']);
        $routes->post('(:num)',    'EingangsrechnungenController::update/$1', ['as' => 'eingangsrechnungen.update']);
        $routes->post('(:num)/bezahlt', 'EingangsrechnungenController::alsBezahlt/$1', ['as' => 'eingangsrechnungen.bezahlt']);
        $routes->post('(:num)/loeschen', 'EingangsrechnungenController::delete/$1', ['as' => 'eingangsrechnungen.delete']);
    });

    /****************************************************************************
    * ProfileController *
    *****************************************************************************/

    $routes->get('profile', 'ProfileController::edit', ['filter' => 'session']);
    $routes->post('profile', 'ProfileController::update', ['filter' => 'session']);
    $routes->post('profile/avatar', 'ProfileController::uploadAvatar', ['filter' => 'session']);
});
