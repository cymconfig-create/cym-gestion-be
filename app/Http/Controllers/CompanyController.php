<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Company\CompanyService;
use App\Services\Company\CreateCompany\CreateCompanyService;
use App\Services\Company\UpdateCompany\UpdateCompanyService;
use App\Services\Company\DeleteCompany\DeleteCompanyService;

class CompanyController extends Controller
{
    private $createCompanyService;
    private $updateCompanyService;
    private $deleteCompanyService;
    private $companyService;

    public function __construct(
        CompanyService $companyService,
        CreateCompanyService $createCompanyService,
        UpdateCompanyService $updateCompanyService,
        DeleteCompanyService $deleteCompanyService
    ) {
        $this->createCompanyService = $createCompanyService;
        $this->updateCompanyService = $updateCompanyService;
        $this->deleteCompanyService = $deleteCompanyService;
        $this->companyService = $companyService;
    }

    public function all()
    {
        return $this->companyService->all();
    }

    public function find($company_id)
    {
        return $this->companyService->find($company_id);
    }

    public function findBy($colum, $company_id)
    {
        return $this->companyService->findBy($colum, $company_id);
    }

    public function findByAll($colum, $value)
    {
        return $this->companyService->findByAll($colum, $value);
    }

    public function create(Request $request)
    {
        return $this->createCompanyService->create($request);
    }

    public function update(Request $request, $company_id)
    {
        return $this->updateCompanyService->update($request, $company_id);
    }

    public function delete($company_id)
    {
        return $this->deleteCompanyService->delete($company_id);
    }
}
