<?php

namespace AcDevelopers\EloquentRepository\Contracts;

/**
 * Interface CriteriaInterface
 *
 * @package AcDevelopers\EloquentRepository\Contracts
 */
interface CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository);
}