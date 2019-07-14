<?php

namespace AcDevelopers\EloquentRepository\Events;

/**
 * Class RepositoryEntityCreated
 *
 * @package AcDevelopers\EloquentRepository\Events
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "created";
}
