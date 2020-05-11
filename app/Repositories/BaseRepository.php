<?php

namespace BeInMedia\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepository
{

    /**
     * @return $model
     */
    public function getModel();

    /**
     * @param int $id
     * @return $model
     */
    public function find($id);

    /**
     * @param int $uuid
     * @return $model
     */
    public function findByUuid($uuid);

    /**
     * Return a collection of all elements of the resource
     * @return Collection
     */
    public function all();

    /**
     * Paginate the model to $perPage items per page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 15, $conditions = null);

    /**
     * Create a resource
     * @param  $data
     * @return $model
     */
    public function create($data);

    /**
     * Update a resource
     * @param  $model
     * @param array $data
     * @return $model
     */
    public function update($model, $data);

    /**
     * Destroy a resource
     * @param  $model
     * @return bool
     */
    public function destroy($model);

    /**
     * Find a resource by the given slug
     * @param string $slug
     * @return $model
     */
    public function findBySlug($slug);

    /**
     * Find a resource by an array of attributes
     * @param array $attributes
     * @return $model
     */
    public function findByAttributes(array $attributes);

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return Collection
     */
    public function findByMany(array $ids);

    /**
     * Return a collection of element who's id match
     * @param  $id
     * @return $model
     */
    public function findOrFail($id);

    /**
     * Get resources by an array of attributes
     * @param array $attributes
     * @param null|string $orderBy
     * @param string $sortOrder
     * @return Collection
     */
    public function getByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc');

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache();
}
