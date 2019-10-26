<?php

namespace AcDevelopers\EloquentRepository;

use AcDevelopers\EloquentRepository\Contracts\CriteriaInterface;
use AcDevelopers\EloquentRepository\Contracts\RepositoryCriteriaInterface;
use AcDevelopers\EloquentRepository\Contracts\RepositoryInterface;
use AcDevelopers\EloquentRepository\Events\RepositoryEntityCreated;
use AcDevelopers\EloquentRepository\Events\RepositoryEntityDeleted;
use AcDevelopers\EloquentRepository\Events\RepositoryEntityUpdated;
use Illuminate\Container\Container as Application;
use AcDevelopers\EloquentRepository\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Repository
 *
 * @package AcDevelopers\EloquentRepository
 */
abstract class BaseRepository implements RepositoryInterface, RepositoryCriteriaInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Builder|Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * Collection of Criteria
     *
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * @var \Closure
     */
    protected $scopeQuery = null;

    /**
     * Repository constructor.
     *
     * @param Application $app
     * @throws RepositoryException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->criteria = new Collection();
        $this->makeModel();
        $this->boot();
    }

    /**
     *
     */
    public function boot()
    {}

    /**
     * Reset the model property.
     *
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Make a new instance of the created model.
     *
     * @return Model|mixed
     * @throws RepositoryException
     */
    public function makeModel()
    {
        try {
            $model = $this->app->make($this->model());
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $exception) {
            throw new RepositoryException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();
        $this->resetScope();

        return $results;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     * @return void
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria()
    {

        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaInterface) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    /**
     * Alias of All method
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function get($columns = ['*'])
    {
        return $this->all($columns);
    }

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function getByCriteria(CriteriaInterface $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        $results = $this->model->get();
        $this->resetModel();

        return $results;
    }

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function create(array $attributes)
    {
        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        if (function_exists('event')) {
            event(new RepositoryEntityCreated($this, $model));
        }

        return $model;
    }

    /**
     * Delete a record from the database.
     *
     * @param $id|Model
     * @return bool|mixed|null
     * @throws RepositoryException
     */
    public function delete($id)
    {
        $this->applyScope();

        $model = $this->retrieveModelInstance($id);

        $originalModel = clone $model;

        $this->resetModel();

        try {
            $deleted = $model->delete();
        } catch (\Exception $exception) {
            throw new RepositoryException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if (function_exists('event')) {
            event(new RepositoryEntityDeleted($this, $originalModel));
        }

        return $deleted;
    }

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     * @return bool|mixed|null
     * @throws RepositoryException
     */
    public function deleteWhere(array $where)
    {
        $this->applyScope();

        $this->applyConditions($where);

        try {
            $deleted = $this->model->delete();
        } catch (\Exception $exception) {
            throw new RepositoryException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if (function_exists('event')) {
            event(new RepositoryEntityDeleted($this, $this->model->getModel()));
        }

        $this->resetModel();

        return $deleted;
    }

    /**
     * Find data by id
     *
     * @param $id
     * @param array $columns
     * @return Model
     * @throws RepositoryException
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by field and value
     *
     * @param $field
     * @param null $value
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $this->applyConditions($where);

        $model = $this->model->get($columns);
        $this->resetModel();

        return $model;
    }
    
    /**
     * Find data by between values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findWhereBetween($field, array $values, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereBetween($field, $values)->get($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * Find data by multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     * @throws RepositoryException
     */
    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }


    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     * @return Model
     * @throws RepositoryException
     */
    public function first($columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->first($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function firstOrCreate(array $attributes = [])
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->firstOrCreate($attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function firstOrNew(array $attributes = [])
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->firstOrNew($attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Save a new model and return the instance. Allow mass-assignment.
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function forceCreate(array $attributes)
    {
        $model = $this->model->forceCreate($attributes);
        $this->resetModel();

        if (function_exists('event')) {
            event(new RepositoryEntityCreated($this, $model));
        }

        return $model;
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * This method protects developers from running forceDelete when trait is missing.
     *
     * @param $id|Model
     * @return bool|mixed|null
     * @throws RepositoryException
     */
    public function forceDelete($id)
    {
        $this->applyScope();

        $model = $this->retrieveModelInstance($id);

        $originalModel = clone $model;

        $this->resetModel();

        $deleted = $model->forceDelete();

        if (function_exists('event')) {
            event(new RepositoryEntityDeleted($this, $originalModel));
        }

        return $deleted;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);

        return $this;
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param $column
     * @param string $direction
     * @return $this
     * @throws RepositoryException
     */
    public function orderBy($column, $direction = 'asc')
    {
        try {
            $this->model = $this->model->orderBy($column, $direction);
        } catch (\InvalidArgumentException $exception) {
            throw new RepositoryException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this;
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws RepositoryException
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->paginate($perPage, $columns, $pageName, $page);

        if (function_exists('app')) {
            $results->appends(app('request')->query());
        }

        $this->resetModel();

        return $results;
    }

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck($column, $key = null)
    {
        $this->applyCriteria();

        return $this->model->pluck($column, $key);
    }

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria)
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     * @return $this
     * @throws RepositoryException
     */
    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaInterface) {
            throw new RepositoryException("Class " . get_class($criteria) . " must be an instance of Prettus\\Repository\\Contracts\\CriteriaInterface");
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Reset all Criteria
     *
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Retrieve a model instance if the argument is not an eloquent model.
     *
     * @param int|Model|string $id
     * @return Model
     * @throws RepositoryException
     */
    protected function retrieveModelInstance($id)
    {
        if ($id instanceof Model) {
            $model = $id;
        } else {
            $model = $this->find($id);
        }

        return $model;
    }

    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $this->model->simplePaginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Sync relations
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @param bool $detaching
     * @return mixed
     * @throws RepositoryException
     */
    public function sync($id, $relation, $attributes, $detaching = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @return mixed
     * @throws RepositoryException
     */
    public function syncWithoutDetaching($id, $relation, $attributes)
    {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Update a entity in repository by id
     *
     * @param $id
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function update($id, array $attributes)
    {
        $this->applyScope();
        
        $model = $this->retrieveModelInstance($id);;
        $model->fill($attributes);
        $model->save();

        $this->resetModel();

        if (function_exists('event')) {
            event(new RepositoryEntityUpdated($this, $model));
        }

        return $model;
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     * @throws RepositoryException
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->applyScope();

        $model = $this->model->updateOrCreate($attributes, $values);

        $this->resetModel();

        if (function_exists('event')) {
            event(new RepositoryEntityUpdated($this, $model));
        }

        return $model;
    }

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);

        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Add sub-select queries to count the relations.
     *
     * @param  mixed $relations
     * @return $this
     */
    public function withCount($relations)
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }
}
