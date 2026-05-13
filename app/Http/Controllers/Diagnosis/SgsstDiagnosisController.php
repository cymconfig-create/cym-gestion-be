<?php

namespace App\Http\Controllers\Diagnosis;

use App\Http\Controllers\Controller;
use App\Services\Diagnosis\SgsstDiagnosisService;

class SgsstDiagnosisController extends Controller
{
    private SgsstDiagnosisService $service;

    public function __construct(SgsstDiagnosisService $service)
    {
        $this->service = $service;
    }

    public function companiesOverview()
    {
        return $this->service->companiesOverview();
    }

    public function companyDetail(int $companyId)
    {
        return $this->service->companyDetail($companyId);
    }

    public function employeeDetail(int $employeeId)
    {
        return $this->service->employeeDetail($employeeId);
    }
}
