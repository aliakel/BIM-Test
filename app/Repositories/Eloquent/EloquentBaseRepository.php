<?php

namespace BeInMedia\Repositories\Eloquent;

use BeInMedia\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentBaseRepository
 *
 * @package BeInMedia\Repositories\Eloquent
 */
abstract class EloquentBaseRepository implements BaseRepository
{
    /**
     * @var Model An instance of the Eloquent Model
     */
    protected $model;
    protected $cachTime;
    protected $entityName;

    /**
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
        $this->cachTime = 0;
        $this->entityName = get_class($model);
    }

    // Setters and getters Auto Generate
    public function __call($function, $args)
    {
        $functionType = strtolower(substr($function, 0, 3));
        $propName = lcfirst(substr($function, 3));
        switch ($functionType) {
            case 'get':
                if (property_exists($this, $propName)) {
                    return $this->$propName;
                }
                break;
            case 'set':
                if (property_exists($this, $propName)) {
                    $this->$propName = $args[0];
                }
                break;
        }
    }

    /**
     * @return $model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @param null $with
     * @return mixed
     * @throws Exception
     */
    public function find($id, $with = null)
    {
        if (!is_null($with)) {
            return $this->model->with($with)->find($id);
        }
        return $this->model->find($id);
    }

    /**
     * Return a collection of element who's id match
     * @param  $id
     * @return Collection
     */
    public function findOrFail($id){
        return $this->model->findOrFail($id);
    }

    /**\
     * @param int $uuid
     * @param null $with
     * @return mixed
     * @throws Exception
     */
    public function findByUuid($uuid, $with = null)
    {
        return cache()->remember($this->entityName . $uuid, $this->cachTime, function () use ($uuid, $with) {
            if (!is_null($with)) {
                return $this->model->where('uuid', $uuid)->with($with)->first();
            }
            return $this->model->where('uuid', $uuid)->first();
        });
    }

    /**
     * @inheritdoc
     */
    public function all($with = null)
    {
        if (!is_null($with)) {
            return $this->model->with($with)->orderBy('id', 'DESC')->get();
        }
        return $this->model->orderBy('id', 'DESC')->get();
    }


    /**
     * @inheritdoc
     */
    public function paginate($perPage = 15, $conditions = null, $with = null)
    {
        $query = $this->model->newQuery();
        if (!is_null($with)) {
            $query = $query->with($with);
        }
        if (!is_null($conditions)) {
            $query = $query->where($conditions);
        }
        return $query->orderBy('id', 'DESC')->paginate($perPage);
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update($model, $data)
    {
        $model->update($data);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function destroy($model)
    {
        return $model->delete();
    }


    /**
     * @inheritdoc
     */
    public function findBySlug($slug, $with = null)
    {
        if (!is_null($with)) {
            return $this->model->with($with)->where('slug', $slug)->first();
        }
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * @inheritdoc
     */
    public function findByAttributes(array $attributes, $with = null)
    {
        $query = $this->buildQueryByAttributes($attributes);
        if (is_null($with)) {
            return $query->first();
        }
        return $query->with($with)->first();
    }

    /**
     * Build Query to catch resources by an array of attributes and params
     * @param array $attributes
     * @param null|string $orderBy
     * @param string $sortOrder
     * @return Builder
     */
    private function buildQueryByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->model->query();

        foreach ($attributes as $field => $value) {
            $query = $query->where($field, $value);
        }

        if (null !== $orderBy) {
            $query->orderBy($orderBy, $sortOrder);
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function getByAttributes(array $attributes, $with = null, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->buildQueryByAttributes($attributes, $orderBy, $sortOrder);
        if (!is_null($with)) {
            return $query->with($with)->get();
        }

        return $query->get();
    }

    /**
     * @inheritdoc
     */
    public function findByMany(array $ids)
    {
        $query = $this->model->query();

        if (method_exists($this->model, 'translations')) {
            $query = $query->with('translations');
        }

        return $query->whereIn("id", $ids)->get();
    }

    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        return true;
    }
}
