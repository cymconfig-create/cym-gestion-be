<?php

namespace App\Services\Shared;

use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DocumentPathService extends Service
{
    // Si mes es true, se agrega el nombre del mes en  la ruta.
    public function saveDocumentInPath($codeCompany, $codeDocument, $newName, $file, $mes = false)
    {
        $path = $this->generateStoragePath($codeCompany, $codeDocument, $mes);
        $filename = $this->generateFileName($newName, $file);

        $storedPath = Storage::putFileAs($path, $file, $filename);
        return str_replace('public/', '', $storedPath);
    }

    private function generateStoragePath($company, $document, $includeMonth)
    {
        $year = now()->year;
        $month = strtoupper(now()->translatedFormat('F')); // Ej: ABRIL, MAYO

        return $includeMonth
            ? "public/documents/{$year}/{$company}/{$document}/{$month}"
            : "public/documents/{$year}/{$company}/{$document}";
    }

    private function generateFileName($name, $file)
    {
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $timestamp = now()->format('Y_m_d_H_i_s');
        $cleaned = $this->cleanString($name);

        return "{$cleaned}_{$timestamp}.{$extension}";
    }

    public function cleanString($value)
    {
        $value = strtolower($value);

        $replacements = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'à' => 'a',
            'è' => 'e',
            'ì' => 'i',
            'ò' => 'o',
            'ù' => 'u',
            'ä' => 'a',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'o',
            'ü' => 'u',
            'ñ' => 'n'
        ];

        $value = strtr($value, $replacements);
        $value = preg_replace('/[^a-z0-9\s]/', '', $value);
        $value = preg_replace('/\s+/', '_', $value);
        $value = preg_replace('/_+/', '_', $value);

        return trim($value, '_');
    }
}
