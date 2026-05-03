import { GoogleGenAI, Type } from "@google/genai";

const ai = new GoogleGenAI({ apiKey: process.env.GEMINI_API_KEY });

export interface ExtractedData {
  vendor: string;
  date: string;
  invoiceNumber?: string;
  totalAmount: number;
  netAmount?: number;
  taxAmount: number;
  currency: string;
  iban?: string;
  lineItems: Array<{
    description: string;
    amount: number;
  }>;
}

export async function extractDataFromFile(base64Data: string, mimeType: string): Promise<ExtractedData> {
  const prompt = "Extrahiere die folgenden Informationen aus dem angehängten Beleg (Bild oder PDF) als JSON: Verkäufer (vendor), Datum (date), Rechnungsnummer (invoiceNumber), Gesamtbetrag (totalAmount), Nettobetrag (netAmount), Steuerbetrag (taxAmount), Währung (currency), IBAN des Empfängers (iban) und eine Liste der Einzelposten (lineItems mit description und amount). Validieren Sie die IBAN (nur das Format prüfen). Antworte ausschließlich im JSON-Format.";

  const response = await ai.models.generateContent({
    model: "gemini-3-flash-preview",
    contents: [
      {
        parts: [
          { text: prompt },
          {
            inlineData: {
              mimeType: mimeType,
              data: base64Data,
            },
          },
        ],
      },
    ],
    config: {
      responseMimeType: "application/json",
      responseSchema: {
        type: Type.OBJECT,
        properties: {
          vendor: { type: Type.STRING, description: "Der Name des Geschäfts oder Dienstleisters" },
          date: { type: Type.STRING, description: "Das Rechnungsdatum im Format YYYY-MM-DD" },
          invoiceNumber: { type: Type.STRING, description: "Die Rechnungsnummer" },
          totalAmount: { type: Type.NUMBER, description: "Der Brutto-Gesamtbetrag" },
          netAmount: { type: Type.NUMBER, description: "Der Netto-Gesamtbetrag" },
          taxAmount: { type: Type.NUMBER, description: "Der enthaltene Steuerbetrag" },
          currency: { type: Type.STRING, description: "Die Währung (z.B. EUR, USD)" },
          iban: { type: Type.STRING, description: "Die im Beleg gefundene IBAN des Zahlungsempfängers" },
          lineItems: {            type: Type.ARRAY,
            items: {
              type: Type.OBJECT,
              properties: {
                description: { type: Type.STRING },
                amount: { type: Type.NUMBER }
              },
              required: ["description", "amount"]
            }
          }
        },
        required: ["vendor", "date", "totalAmount", "currency"]
      }
    }
  });

  const text = response.text;
  if (!text) throw new Error("Keine Antwort von Gemini erhalten.");
  
  return JSON.parse(text) as ExtractedData;
}
