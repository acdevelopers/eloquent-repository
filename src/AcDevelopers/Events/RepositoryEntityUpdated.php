<?php

namespace AcDevelopers\EloquentRepository\Events;

/**
 * Class RepositoryEntityUpdated
 *
 * @package AcDevelopers\EloquentRepository\Events
 */
class RepositoryEntityUpdated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "updated";
}
