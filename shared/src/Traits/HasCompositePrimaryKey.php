<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasCompositePrimaryKey
{
    /**
     * Set the keys for a save update query.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        // @phpstan-ignore-next-line
        assert(is_array($keys), 'Composite primary key must be an array');
        return $query->where(function ($q) use ($keys) {
            foreach ($keys as $key) {
                $q->where($key, '=', $this->getAttribute($key));
            }
        });
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        if ($this->getIncrementing()) {
            return array_merge([$this->getKeyName() => $this->getKeyType()], $this->casts);
        }
        return $this->casts;
    }

    /**
     *
     * Get if the IDs are incrementing.
     *
     * @SuppressWarnings(PHPMD)
     * @return boolean
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        $fields = $this->getKeyName();

        // @phpstan-ignore-next-line
        assert(is_array($fields), 'Composite primary key must be an array');

        return array_map(function ($key) {
            return $this->getAttribute($key);
        }, (array)$fields);
    }

    /**
     * Finds model by primary keys
     *
     * @param array $ids
     * @return mixed
     */
    public static function find(array $ids)
    {
        $modelClass = self::class;
        $model = new $modelClass();
        $keys = $model->primaryKey;
        // @phpstan-ignore-next-line
        assert(is_array($keys), 'Composite primary key must be an array');

        return $model::query()->where(function ($query) use ($ids, $keys) {
            foreach ($keys as $idx => $key) {
                if (isset($ids[$idx])) {
                    $query->where($key, $ids[$idx]);
                } else {
                    $query->whereNull($key);
                }
            }
        })->first();
    }

    /**
     * Find model by primary key or throws ModelNotFoundException
     *
     * @param array $ids
     * @return mixed
     */
    public static function findOrFail(array $ids)
    {
        $modelClass = self::class;
        $model = new $modelClass();
        $record = $model->find($ids);
        if (!$record) {
            throw new ModelNotFoundException();
        }
        return $record;
    }
}
