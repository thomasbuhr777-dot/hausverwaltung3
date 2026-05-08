<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AusstattungsmerkmalModel;

class Ausstattungsmerkmale extends BaseController
{
    public function index()
    {
        $model = new AusstattungsmerkmalModel();

        $data = [
            'title' => 'Ausstattungsmerkmale',
            'gruppen' => $model->getGroupedByKategorie(),
        ];

        return view('ausstattungsmerkmale/index', $data);
    }
}