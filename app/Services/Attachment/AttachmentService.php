<?php

namespace App\Services\Attachment;

use App\Repositories\AttachmentRepository;
use App\Services\Service;
use App\Util\Constants;
use InvalidArgumentException;

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
        try {
            $documentCompany = $this->repository->findBy($column, $id);
        } catch (InvalidArgumentException $e) {
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        if (!$documentCompany) {
            return $this->resolve(false, null, $documentCompany, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        // El servicio ahora solo devuelve el objeto
        return $this->resolve(false, Constants::NOT_MESSAGE, $documentCompany, null);
    }

    public function findByAll($column, $value)
    {
        try {
            $attachments = $this->repository->findByAll($column, $value);
        } catch (InvalidArgumentException $e) {
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        $status = empty($attachments) ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(false, Constants::NOT_MESSAGE, $attachments, $status);
    }

    public function findByAllAttributes($attributes)
    {
        try {
            $attachment = $this->repository->findByAllAttributes($attributes);
        } catch (InvalidArgumentException $e) {
            return $this->resolve(true, $e->getMessage(), Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        if (empty($attachment)) {
            return $this->resolve(false, Constants::NOT_MESSAGE, Constants::OBJECT_NOT_FOUND, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        return $this->resolve(false, Constants::NOT_MESSAGE, $attachment, null);
    }
}
