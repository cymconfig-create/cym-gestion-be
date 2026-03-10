<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Document\DocumentService;

class DocumentController extends Controller
{
    private $documentService;

    public function __construct(
        DocumentService $documentService
    ) {
        $this->documentService = $documentService;
    }

    public function findBy($colum, $value)
    {
        return $this->documentService->findBy($colum, $value);
    }
}
