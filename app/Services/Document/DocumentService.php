<?php

namespace App\Services\Document;

use App\Repositories\DocumentRepository;
use App\Services\Service;
use App\Util\Constants;

class DocumentService extends Service
{
    protected $repository;

    public function __construct(
        DocumentRepository $repository,
    ) {
        $this->repository = $repository;
    }

    public function all()
    {
        $document = $this->repository->all();
        $status = empty($attachment) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $document, $status);
    }

    public function findBy($column, $value)
    {
        return $this->repository->findBy($column, $value);
    }

    public function find($id)
    {
        $document = $this->repository->find($id);
        $status = empty($document) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $document, $status);
    }


    public function findByAll($colum, $value)
    {
        $documents = $this->repository->findByAll($colum, $value);
        $status = empty($documents) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $documents, $status);
    }

    /**
     * Obtiene todos los documentos para uso interno sin formatear la respuesta.
     *
     */
    public function getAllDocumentTypes()
    {
        return $this->repository->all();
    }
}
