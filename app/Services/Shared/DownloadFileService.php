<?php

namespace App\Services\Shared;

use App\Services\Service;
use App\Util\Constants;

class DownloadFileService extends Service
{
    protected $zipService;

    public function __construct(ZipService $zipService)
    {
        $this->zipService = $zipService;
    }

    // Si llega un solo archivo lo muestra pero, si llegan varios archivos los comprime en .zip
    public function serveFiles($documentCompanies)
    {
        if (count($documentCompanies) === 1) {
            return $this->downloadFile($documentCompanies[0]->route_file);
        }

        $zipPath = $this->zipService->createZip($documentCompanies);

        if (!$zipPath) {
            return $this->resolve(true, Constants::ERROR_TO_CREATE_ZIP, Constants::NOT_DATA, Constants::CODE_INTERNAL_SERVER_ERROR);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function downloadFile($routeFile)
    {
        $filePath = storage_path('app/public/' . $routeFile);

        if (!file_exists($filePath)) {
            return $this->resolve(true, Constants::FILE_NO_FOUND, Constants::NOT_DATA, Constants::CODE_NOT_FOUND);
        }

        return response()->download($filePath);
    }
}
