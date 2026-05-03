/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useRef } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  Upload, 
  FileText, 
  Loader2, 
  CheckCircle2, 
  AlertCircle, 
  ArrowRight,
  Receipt,
  Euro,
  Calendar,
  Building2,
  Trash2,
  FileJson,
  CreditCard
} from 'lucide-react';
import { extractDataFromFile, ExtractedData } from './services/geminiService';

export default function App() {
  const [selectedImage, setSelectedImage] = useState<string | null>(null);
  const [mimeType, setMimeType] = useState<string>('');
  const [isExtracting, setIsExtracting] = useState(false);
  const [result, setResult] = useState<ExtractedData | null>(null);
  const [tempFilename, setTempFilename] = useState<string>('');
  const [isSaved, setIsSaved] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      processFile(file);
    }
  };

  const processFile = (file: File) => {
    const isImage = file.type.startsWith('image/');
    const isPdf = file.type === 'application/pdf';

    if (!isImage && !isPdf) {
      setError('Bitte laden Sie ein Bild (JPG, PNG) oder eine PDF-Datei hoch.');
      return;
    }

    setMimeType(file.type);
    setError(null);
    setResult(null);
    setIsSaved(false);
    // Simulate CI4 getRandomName()
    setTempFilename(Math.random().toString(36).substring(7) + '.' + file.name.split('.').pop());

    const reader = new FileReader();
    reader.onload = () => {
      setSelectedImage(reader.result as string);
    };
    reader.readAsDataURL(file);
  };

  const handleExtract = async () => {
    if (!selectedImage) return;

    setIsExtracting(true);
    setError(null);

    try {
      // Remove data:...;base64, prefix
      const base64Data = selectedImage.split(',')[1];
      const data = await extractDataFromFile(base64Data, mimeType);
      setResult(data);
    } catch (err) {
      console.error(err);
      setError('Fehler bei der Extraktion. Bitte versuchen Sie es erneut.');
    } finally {
      setIsExtracting(false);
    }
  };

  const handleSave = () => {
    setIsSaved(true);
    setTimeout(() => {
      reset();
    }, 2000);
  };

  const reset = () => {
    setSelectedImage(null);
    setResult(null);
    setError(null);
    setIsSaved(false);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  return (
    <div className="min-h-screen bg-[#F9FAFB] pb-20">
      {/* Header */}
      <header className="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div className="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <div className="bg-indigo-600 p-1.5 rounded-lg">
              <Receipt className="w-5 h-5 text-white" />
            </div>
            <span className="font-semibold text-xl tracking-tight">BelegFix</span>
          </div>
          <div className="text-xs font-medium text-gray-500 uppercase tracking-widest bg-gray-100 px-2 py-1 rounded">
            Human-in-the-Loop OCR
          </div>
        </div>
      </header>

      <main className="max-w-5xl mx-auto px-4 pt-12">
        <div className="flex flex-col gap-12">
          {/* Hero Section */}
          <section className="text-center space-y-4">
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
              Review & <span className="text-indigo-600">Freigabe</span>.
            </h1>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Daten prüfen, korrigieren und direkt speichern. Volle Kontrolle über Ihre Buchhaltung.
            </p>
          </section>

          {/* Main Interaction Area */}
          <div className="grid md:grid-cols-2 gap-8 items-start">
            
            {/* Upload Area */}
            <div className="space-y-6">
              {!selectedImage ? (
                <motion.div 
                  id="drop-zone"
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  className="relative group h-96 border-2 border-dashed border-gray-300 rounded-2xl flex flex-col items-center justify-center bg-white hover:border-indigo-400 hover:bg-indigo-50/30 transition-all cursor-pointer overflow-hidden p-8"
                  onClick={() => fileInputRef.current?.click()}
                >
                  <div className="bg-indigo-50 p-6 rounded-full group-hover:scale-110 transition-transform mb-4">
                    <Upload className="w-8 h-8 text-indigo-600" />
                  </div>
                  <h3 className="text-xl font-medium text-gray-900">Datei hochladen</h3>
                  <p className="text-gray-500 text-center mt-2">
                    Klicken oder Drag & Drop<br />
                    <span className="text-sm">(JPG, PNG, WebP, PDF)</span>
                  </p>
                  <input 
                    type="file" 
                    ref={fileInputRef}
                    onChange={handleFileChange}
                    className="hidden" 
                    accept="image/*,application/pdf"
                  />
                </motion.div>
              ) : (
                <motion.div 
                  initial={{ opacity: 0, scale: 0.95 }}
                  animate={{ opacity: 1, scale: 1 }}
                  className="glass-card rounded-2xl p-4 space-y-4 overflow-hidden shadow-lg border-indigo-100"
                >
                  <div className="relative aspect-[3/4] md:aspect-auto md:h-96 rounded-xl overflow-hidden bg-gray-100">
                    {mimeType === 'application/pdf' ? (
                      <div className="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                        <FileJson className="w-16 h-16 mb-4 text-indigo-200" />
                        <span className="font-medium">Digitale PDF-Rechnung</span>
                        <span className="text-xs mt-1 text-gray-400 px-3 py-1 bg-white rounded shadow-sm border border-gray-100">
                          {tempFilename}
                        </span>
                      </div>
                    ) : (
                      <img 
                        src={selectedImage} 
                        alt="Uploaded" 
                        className="w-full h-full object-contain"
                      />
                    )}
                    <button 
                      onClick={reset}
                      className="absolute top-4 right-4 bg-white/90 hover:bg-red-50 text-red-600 p-2 rounded-lg shadow-sm transition-colors"
                    >
                      <Trash2 className="w-5 h-5" />
                    </button>
                  </div>
                  {!result && (
                    <button 
                      onClick={handleExtract}
                      disabled={isExtracting}
                      className="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium flex items-center justify-center gap-2 disabled:opacity-50 transition-all shadow-md active:scale-[0.98]"
                    >
                      {isExtracting ? (
                        <>
                          <Loader2 className="w-5 h-5 animate-spin" />
                          Extrahiere Daten...
                        </>
                      ) : (
                        <>
                          Daten analysieren <ArrowRight className="w-5 h-5" />
                        </>
                      )}
                    </button>
                  )}
                </motion.div>
              )}

              {error && (
                <div className="p-4 bg-red-50 border border-red-100 rounded-xl flex items-center gap-3 text-red-700">
                  <AlertCircle className="w-5 h-5 shrink-0" />
                  <p className="text-sm font-medium">{error}</p>
                </div>
              )}
            </div>

            {/* Results Area */}
            <div className="space-y-6">
              <AnimatePresence mode="wait">
                {result ? (
                  <motion.div 
                    key="result"
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: -20 }}
                    className="glass-card rounded-2xl p-6 shadow-xl border-indigo-50 space-y-6"
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2 text-indigo-600">
                        <FileText className="w-5 h-5" />
                        <span className="font-semibold text-sm uppercase tracking-wide">Datencheck (Human-in-the-Loop)</span>
                      </div>
                      {isSaved && (
                        <div className="flex items-center gap-1 text-green-600 text-sm font-semibold animate-pulse">
                          <CheckCircle2 className="w-4 h-4" /> Gespeichert!
                        </div>
                      )}
                    </div>

                    <div className="space-y-4">
                      {/* Hidden Input Simulation */}
                      <input type="hidden" name="local_filename" value={tempFilename} />

                      <div className="grid grid-cols-2 gap-4">
                        <div className="col-span-2">
                          <label className="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Verkäufer</label>
                          <input 
                            type="text" 
                            className="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                            value={result.vendor}
                            onChange={(e) => setResult({...result, vendor: e.target.value})}
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Rechnungs-Nr.</label>
                          <input 
                            type="text" 
                            className="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                            value={result.invoiceNumber || ''}
                            onChange={(e) => setResult({...result, invoiceNumber: e.target.value})}
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Datum</label>
                          <input 
                            type="text" 
                            className="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                            value={result.date}
                            onChange={(e) => setResult({...result, date: e.target.value})}
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Netto</label>
                          <div className="relative">
                            <input 
                              type="number" 
                              className="w-full bg-gray-50 border border-gray-200 rounded-lg pl-8 pr-4 py-2 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                              value={result.netAmount || (result.totalAmount - result.taxAmount).toFixed(2)}
                              onChange={(e) => setResult({...result, netAmount: parseFloat(e.target.value)})}
                            />
                            <Euro className="absolute left-3 top-2.5 w-3.5 h-3.5 text-gray-400" />
                          </div>
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Brutto</label>
                          <div className="relative">
                            <input 
                              type="number" 
                              className="w-full bg-indigo-50 border border-indigo-100 rounded-lg pl-8 pr-4 py-2 text-sm font-bold text-indigo-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                              value={result.totalAmount}
                              onChange={(e) => setResult({...result, totalAmount: parseFloat(e.target.value)})}
                            />
                            <Euro className="absolute left-3 top-2.5 w-3.5 h-3.5 text-indigo-400" />
                          </div>
                        </div>
                        <div className="col-span-2">
                          <label className="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">IBAN (Empfänger)</label>
                          <div className="relative">
                            <input 
                              type="text" 
                              className="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm font-mono font-medium text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                              value={result.iban || ''}
                              onChange={(e) => setResult({...result, iban: e.target.value})}
                            />
                            <CreditCard className="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
                          </div>
                        </div>
                      </div>

                      <div className="pt-6">
                        <button 
                          onClick={handleSave}
                          disabled={isSaved}
                          className={`w-full py-3 rounded-xl font-semibold flex items-center justify-center gap-2 transition-all shadow-md ${
                            isSaved ? 'bg-green-100 text-green-700' : 'bg-indigo-600 hover:bg-indigo-700 text-white active:scale-[0.98]'
                          }`}
                        >
                          {isSaved ? "Daten erfolgreich gespeichert!" : "Review abschließen & Speichern"}
                        </button>
                        <p className="text-[10px] text-gray-400 text-center mt-3 uppercase tracking-tighter">
                          Interne Referenz: {tempFilename}
                        </p>
                      </div>
                    </div>
                  </motion.div>
                ) : (
                  <motion.div 
                    key="placeholder"
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    className="h-full border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center p-12 text-center"
                  >
                    <div className="bg-gray-100 p-4 rounded-full mb-4">
                      <FileText className="w-8 h-8 text-gray-300" />
                    </div>
                    <h4 className="text-gray-400 font-medium">Kontrollansicht</h4>
                    <p className="text-sm text-gray-400 max-w-[200px] mt-2 italic">
                      Nach der Analyse können Sie hier die Daten anpassen und freigeben.
                    </p>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>
          </div>
        </div>
      </main>

      {/* Footer / Features */}
      <footer className="mt-24 border-t border-gray-100 py-12">
        <div className="max-w-5xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
          <div className="space-y-2">
            <h5 className="font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">Human-in-the-Loop</h5>
            <p className="text-sm text-gray-500 leading-relaxed">
              KI liefert die Basis, Sie behalten die Kontrolle. Jedes Feld kann vor dem Speichern manuell angepasst werden.
            </p>
          </div>
          <div className="space-y-2">
            <h5 className="font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">IBAN Validierung</h5>
            <p className="text-sm text-gray-500 leading-relaxed">
              Die IBAN wird extrahiert und die KI prüft direkt das Format, um Fehler beim Zahlungsverkehr zu vermeiden.
            </p>
          </div>
          <div className="space-y-2">
            <h5 className="font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">CI4 Kompatibel</h5>
            <p className="text-sm text-gray-500 leading-relaxed">
              Strukturierte Datenübergabe inklusive zufälligem Dateinamen für eine nahtlose Integration in Ihre Web-App.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}
