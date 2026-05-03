<?php

namespace App\Controllers;

use App\Models\BelegModel;
use CodeIgniter\Controller;

class BelegController extends Controller
{
    private $apiKey = 'DEIN_GEMINI_API_KEY'; // In .env speichern!

    public function index()
    {
        return view('belege/upload');
    }

    /**
     * Schritt 1: Datei hochladen und KI Analyse starten
     */
    public function analyze()
    {
        $file = $this->request->getFile('beleg');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Ungültige Datei');
        }

        // Datei lokal speichern (Best Practice CI4)
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/belege', $newName);

        // Datei für Gemini vorbereiten (Base64)
        $filePath = WRITEPATH . 'uploads/belege/' . $newName;
        $base64Data = base64_encode(file_get_contents($filePath));
        $mimeType = $file->getMimeType();

        // Gemini API Aufruf
        $extractedData = $this->callGeminiOCR($base64Data, $mimeType);

        // Review View laden
        return view('belege/review', [
            'data'     => $extractedData,
            'filename' => $newName
        ]);
    }

    /**
     * Schritt 2: Manuelle Kontrolle speichern
     */
    public function store()
    {
        $model = new BelegModel();

        $data = [
            'vendor'         => $this->request->getPost('vendor'),
            'invoice_number' => $this->request->getPost('invoice_number'),
            'date'           => $this->request->getPost('date'),
            'total_amount'   => $this->request->getPost('total_amount'),
            'net_amount'     => $this->request->getPost('net_amount'),
            'tax_amount'     => $this->request->getPost('tax_amount'),
            'currency'       => $this->request->getPost('currency'),
            'iban'           => $this->request->getPost('iban'),
            'filename'       => $this->request->getPost('filename'),
        ];

        if ($model->insert($data)) {
            return redirect()->to('/belege')->with('success', 'Beleg erfolgreich verbucht.');
        }

        return redirect()->back()->with('error', 'Fehler beim Speichern.');
    }

    private function callGeminiOCR($base64, $mime)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $this->apiKey;

        $prompt = "Extrahiere Verkäufer (vendor), Datum (date, YYYY-MM-DD), Rechnungsnummer (invoiceNumber), Gesamtbetrag (totalAmount), Nettobetrag (netAmount), Steuerbetrag (taxAmount), Währung (currency) und IBAN (iban). Antworte NUR in validem JSON.";

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt],
                        [
                            "inline_data" => [
                                "mime_type" => $mime,
                                "data"      => $base64
                            ]
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "response_mime_type" => "application/json"
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return json_decode($result['candidates'][0]['content']['parts'][0]['text'], true);
    }
}
