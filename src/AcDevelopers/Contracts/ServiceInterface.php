<?php

namespace AcDevelopers\EloquentRepository\Contracts;


/**
 * Class BaseService
 *
 * @package AcDevelopers\EloquentRepository
 */
interface ServiceInterface
{
    /**
     * Get repository.
     *
     * @return \AcDevelopers\EloquentRepository\Contracts\RepositoryInterface
     */
    public function getRepository();
}