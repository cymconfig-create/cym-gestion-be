<?php

namespace App\Http\Controllers\Ia;

use App\Http\Controllers\Controller;
use App\Services\Ia\DocumentUploadCompletionService;
use Illuminate\Http\Request;

class DocumentCompletionController extends Controller
{
    public function __construct(
        private DocumentUploadCompletionService $documentUploadCompletionService
    ) {
    }

    public function uploadCompletion(Request $request)
    {
        return $this->documentUploadCompletionService->uploadCompletion($request);
    }
}
