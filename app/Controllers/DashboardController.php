<?php

namespace App\Controllers;

use App\Models\ObjektModel;
use App\Models\EinheitModel;
use App\Models\MietvertragModel;
use App\Models\ZahlungModel;
use App\Models\EingangsrechnungModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $objektModel   = new ObjektModel();
        $einheitModel  = new EinheitModel();
        $vertragModel  = new MietvertragModel();
        $zahlungModel  = new ZahlungModel();
        $rechnungModel = new EingangsrechnungModel();

        // KPIs
        $stats = [
            'objekte_gesamt'     => $objektModel->countAll(),
            'einheiten_gesamt'   => $einheitModel->countAll(),
            'einheiten_vermietet' => $einheitModel->where('status', 'vermietet')->countAllResults(),
            'einheiten_frei'     => $einheitModel->where('status', 'verfuegbar')->countAllResults(),
            'vertraege_aktiv'    => $vertragModel->where('status', 'aktiv')->countAllResults(),
            'zahlungen_offen'    => $zahlungModel->whereIn('status', ['offen', 'ueberfaellig'])->countAllResults(),
            'rechnungen_offen'   => $rechnungModel->whereIn('status', ['offen', 'ueberfaellig'])->countAllResults(),
        ];

        // Monatliche Einnahmen aktuelles Jahr
        $zahlungModel2 = new ZahlungModel();
        $einnahmen = $zahlungModel2->getMonatlicheEinnahmen((int) date('Y'));

        // Auslaufende Verträge
        $vertragModel2 = new MietvertragModel();
        $auslaufend = $vertragModel2->getAuslaufendeVertraege(90);

        // Überfällige Rechnungen
        $rechnungModel2 = new EingangsrechnungModel();
        $ueberfaellige  = $rechnungModel2->getUeberfaelligeRechnungen();

        return view('dashboard/index', [
            'title'           => 'Dashboard',
            'stats'           => $stats,
            'einnahmen'       => $einnahmen,
            'auslaufend'      => $auslaufend,
            'ueberfaellig_rechnungen' => $ueberfaellige,
        ]);
    }
}
