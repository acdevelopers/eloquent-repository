<?php

namespace AcDevelopers\EloquentRepository\Contracts;


use AcDevelopers\EloquentRepository\BaseRepository;
use AcDevelopers\EloquentRepository\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Repository
 *
 * @package AcDevelopers\EloquentRepository
 */
interface RepositoryInterface
{
    /**
     *
     */
    public function boot();

    /**
     * Reset the model property.
     *
     * @throws RepositoryException
     */
    public function resetModel();

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model();

    /**
     * Make a new instance of the created model.
     *
     * @return Model|mixed
     * @throws RepositoryException
     */
    public function makeModel();

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable();

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function all($columns = ['*']);

    /**
     * Alias of All method
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function get($columns = ['*']);

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function getByCriteria(CriteriaInterface $criteria);

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria();

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function create(array $attributes);

    /**
     * Delete a record from the database.
     *
     * @param $id |Model
     * @return bool|mixed|null
     * @throws RepositoryException
     */
    public function delete($id);

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     * @return bool|mixed|null
     * @throws RepositoryException
     */
    public function deleteWhere(array $where);

    /**
     * Find data by id
     *
     * @param $id
     * @param array $columns
     * @return Model
     * @throws RepositoryException
     */
    public function find($id, $columns = ['*']);

    /**
     * Find data by field and value
     *
     * @param $field
     * @param null $value
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function findByField($field, $value = null, $columns = ['*']);

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function findWhere(array $where, $columns = ['*']);
    
    /**
     * Find data by between values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findWhereBetween($field, array $values, $columns = ['*']);

    /**
     * Find data by multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws RepositoryException
     */
    public function findWhereIn($field, array $values, $columns = ['*']);

    /**
     * Find data by excluding multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     * @throws RepositoryException
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']);

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     * @return Model
     * @throws RepositoryException
     */
    public function first($columns = ['*']);

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function firstOrCreate(array $attributes = []);

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function firstOrNew(array $attributes = []);

    /**
     * Save a new model and return the instance. Allow mass-assignment.
     *
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function forceCreate(array $attributes);

    /**
     * Force a hard delete on a soft deleted model.
     *
     * This method protects developers from running forceDelete when trait is missing.
     *
     * @param $id |Model
     * @return bool|mixed|null
     * @throws RepositoryException
     */
    public function forceDelete($id);

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation);

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields);

    /**
     * Add an "order by" clause to the query.
     *
     * @param $column
     * @param string $direction
     * @return $this
     * @throws RepositoryException
     */
    public function orderBy($column, $direction = 'asc');

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
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck($column, $key = null);

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria);

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     * @return $this
     * @throws RepositoryException
     */
    public function pushCriteria($criteria);

    /**
     * Reset all Criteria
     *
     * @return $this
     */
    public function resetCriteria();

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope();

    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope);

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

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
    public function sync($id, $relation, $attributes, $detaching = true);

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @return mixed
     * @throws RepositoryException
     */
    public function syncWithoutDetaching($id, $relation, $attributes);

    /**
     * Update a entity in repository by id
     *
     * @param $id
     * @param array $attributes
     * @return Model
     * @throws RepositoryException
     */
    public function update($id, array $attributes);

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     * @throws RepositoryException
     */
    public function updateOrCreate(array $attributes, array $values = []);

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields);

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure);

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations);

    /**
     * Add sub-select queries to count the relations.
     *
     * @param  mixed $relations
     * @return $this
     */
    public function withCount($relations);
}