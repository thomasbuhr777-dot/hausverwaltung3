<?php

namespace App\Controllers;

use App\Models\BelegModel;
use CodeIgniter\Controller;

class BelegController extends Controller
{
    private $apiKey = 'YOUR_GEMINI_API_KEY'; // In .env speichern!

    public function index()
    {
        return view('belege/upload');
    }

    /**
     * Hilfsfunktion um verfügbare Modelle zu listen
     */
    public function listModels()
    {
        $url = "https://generativelanguage.googleapis.com/v1/models?key=" . $this->apiKey;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $this->response->setJSON(json_decode($response, true));
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

        // MIME-Type abrufen, BEVOR die Datei verschoben wird
        $mimeType = $file->getMimeType();

        // Datei lokal speichern (Best Practice CI4)
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/belege', $newName);

        // Datei für Gemini vorbereiten (Base64)
        $filePath = WRITEPATH . 'uploads/belege/' . $newName;
        $base64Data = base64_encode(file_get_contents($filePath));

        try {
            // Gemini API Aufruf
            $extractedData = $this->callGeminiOCR($base64Data, $mimeType);

            // Review View laden
            return view('belege/review', [
                'data'     => $extractedData,
                'filename' => $newName
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'KI-Analyse fehlgeschlagen: ' . $e->getMessage());
        }
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
        // Verwende das verfügbare Modell gemini-2.5-flash (v1 stabil)
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey;

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
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("API Error (HTTP $httpCode): " . $response);
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            throw new \Exception($result['error']['message'] ?? 'Unbekannter API Fehler');
        }

        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception("Keine Daten in der API-Antwort gefunden. (Check Safety Filters or File Size)");
        }

        $text = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean markdown blocks if present
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $text = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $text, $matches)) {
            $text = $matches[1];
        }

        $decoded = json_decode(trim($text), true);
        
        if ($decoded === null) {
            throw new \Exception("JSON Parsing Error: " . json_last_error_msg() . " | Content: " . substr($text, 0, 100));
        }

        return $decoded;
    }
}
