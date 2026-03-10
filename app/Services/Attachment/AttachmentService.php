<?php

namespace App\Services\Attachment;

use App\Repositories\AttachmentRepository;
use App\Services\Service;
use App\Util\Constants;

class AttachmentService extends Service
{
    protected $repository;

    public function __construct(
        AttachmentRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function all()
    {
        $attachment = $this->repository->all();
        $status = empty($attachment) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $attachment, $status);
    }

    public function find($id)
    {
        $documentCompany = $this->repository->find($id);

        if (!$documentCompany) {
            return $this->resolve(true, Constants::OBJECT_NOT_FOUND, null, Constants::CODE_NOT_FOUND);
        }

        // El servicio ahora solo devuelve el objeto
        return $this->resolve(false, Constants::NOT_MESSAGE, $documentCompany, null);
    }

    public function findBy($column, $id)
    {
        $documentCompany = $this->repository->findBy($column, $id);

        if (!$documentCompany) {
            return $this->resolve(false, null, $documentCompany, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        // El servicio ahora solo devuelve el objeto
        return $this->resolve(false, Constants::NOT_MESSAGE, $documentCompany, null);
    }

    public function findByAll($column, $value)
    {
        $attachments = $this->repository->findByAll($column, $value);
        $status = empty($attachments) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $attachments, $status);
    }

    public function findByAllAttributes($attributes)
    {
        $attachment = $this->repository->findByAllAttributes($attributes);

        if (empty($attachment)) {
            return $this->resolve(false, Constants::NOT_MESSAGE, Constants::OBJECT_NOT_FOUND, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        return $this->resolve(false, Constants::NOT_MESSAGE, $attachment, null);
    }
}
