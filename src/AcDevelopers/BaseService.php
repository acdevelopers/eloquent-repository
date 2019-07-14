<?php

namespace AcDevelopers\EloquentRepository;


use AcDevelopers\EloquentRepository\Contracts\RepositoryInterface;
use AcDevelopers\EloquentRepository\Contracts\ServiceInterface;

/**
 * Class BaseService
 *
 * @package AcDevelopers\EloquentRepository
 */
abstract class BaseService implements ServiceInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * BaseService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get repository.
     *
     * @return \AcDevelopers\EloquentRepository\Contracts\RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }
}