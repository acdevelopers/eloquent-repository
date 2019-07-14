<?php

namespace AcDevelopers\EloquentRepository\Events;

/**
 * Class RepositoryEntityDeleted
 *
 * @package AcDevelopers\EloquentRepository\Events
 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "deleted";
}
