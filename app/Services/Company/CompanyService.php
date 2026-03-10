<?php

namespace App\Services\Company;

use App\Repositories\CompanyRepository;
use App\Services\Service;
use App\Traits\LoadCompanyRelationshipsTrait;
use App\Util\Constants;

class CompanyService extends Service
{
    use LoadCompanyRelationshipsTrait;

    private $repository;
    public function __construct(CompanyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $companies = $this->repository->all();
        $status = empty($companies) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadCompanyRelationships($companies);

        return $this->resolve(false, Constants::NOT_MESSAGE, $companies, $status);
    }

    public function find($id)
    {
        $company = $this->repository->find($id);
        $status = empty($company) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadCompanyRelationships($company);

        return $this->resolve(false, Constants::NOT_MESSAGE, $company, $status);
    }

    public function findBy($column, $id)
    {
        $company = $this->repository->findBy($column, $id);
        $status = empty($company) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadCompanyRelationships($company);

        return $this->resolve(false, Constants::NOT_MESSAGE, $company, $status);
    }

    public function findByAll($colum, $value)
    {
        $companies = $this->repository->findByAll($colum, $value);
        $status = empty($companies) ? Constants::CODE_SUCCESS_NO_CONTENT : null;
        $this->loadCompanyRelationships($companies);

        return $this->resolve(false, Constants::NOT_MESSAGE, $companies, $status);
    }
}
