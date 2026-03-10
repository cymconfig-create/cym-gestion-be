<?php

namespace App\Services\Shared;

use App\Services\Service;
use ZipArchive;

class ZipService extends Service
{
    public function createZip($documentCompanies)
    {
        $zipFileName = 'documents_' . time() . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        foreach ($documentCompanies as $document) {
            $filePath = storage_path('app/public/' . $document->route_file);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($filePath));
            }
        }

        $zip->close();
        return $zipPath;
    }
}
