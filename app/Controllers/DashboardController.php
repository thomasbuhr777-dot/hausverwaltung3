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

        $objekte    = $objektModel->getObjekteMitStats();
        $einheiten  = $einheitModel->getEinheitenMitDetails();
        $vertraege  = $vertragModel->getMietvertraegeMitDetails();
        $zahlungen  = $zahlungModel->getZahlungenMitDetails();
        $rechnungen = $rechnungModel->getRechnungenMitDetails();

        // KPIs
        $stats = [
            'objekte_gesamt'      => count($objekte),
            'einheiten_gesamt'    => count($einheiten),
            'einheiten_vermietet' => count(array_filter($einheiten, static fn(array $einheit): bool => $einheit['status'] === 'vermietet')),
            'einheiten_frei'      => count(array_filter($einheiten, static fn(array $einheit): bool => $einheit['status'] === 'verfuegbar')),
            'vertraege_aktiv'     => count(array_filter($vertraege, static fn(array $vertrag): bool => $vertrag['status'] === 'aktiv')),
            'zahlungen_offen'     => count(array_filter($zahlungen, static fn(array $zahlung): bool => in_array($zahlung['status'], ['offen', 'ueberfaellig'], true))),
            'rechnungen_offen'    => count(array_filter($rechnungen, static fn(array $rechnung): bool => in_array($rechnung['status'], ['offen', 'ueberfaellig'], true))),
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
