<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasCompositePrimaryKey
{
    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query): \Illuminate\Database\Eloquent\Builder
    {
        $keys = $this->getKeyName();
        return !is_array($keys) ? parent::setKeysForSaveQuery($query) : $query->where(function ($q) use ($keys) {
            foreach ($keys as $key) {
                $q->where($key, '=', $this->getAttribute($key));
            }
        });
    }

    /**
     * Get the casts array.
     * @psalm-suppress InvalidArrayOffset
     * @return array
     */
    public function getCasts()
    {
        // @phpstan-ignore-next-line
        if ($this->getIncrementing()) {
            // @phpstan-ignore-next-line
            return array_merge([$this->getKeyName() => $this->getKeyType()], $this->casts);
        }
        return $this->casts;
    }

    /**
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey(): mixed
    {
        $fields = $this->getKeyName();
        $keys = [];
        if (is_iterable($fields)) { // Check if $fields is iterable (array or object)
            foreach ($fields as $key) {
                $keys[] = $this->getAttribute($key);
            }
            return $keys;
        }
        $key = $this->getAttribute($fields);
        return $key;
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
        $keys = $model->getKey();
        return $model->where(function ($query) use ($ids, $keys) {
            if (is_iterable($keys)) { // Check if $keys is iterable
                foreach ($keys as $idx => $key) {
                    if (isset($ids[$idx])) {
                        $query->where($key, $ids[$idx]);
                    } else {
                        $query->whereNull($key);
                    }
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
