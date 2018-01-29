<?php

namespace App\Models;

abstract class Model
{
    protected $attributes = [];

    protected $table = '';

    /**
     * Model constructor.
     * @param mixed $source
     */
    public function __construct($source = null)
    {
        if ($source) {
            $this->setAttributes($source);
        }
    }

    /**
     * Set attributes on the model.
     * @param array $values
     */
    protected function setAttributes($values)
    {
       foreach ((array) $values as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get a fresh model instance.
     * @param array $values
     * @return $this
     */
    public function refresh($values)
    {
        return new $this($values);
    }

    public function __get($key)
    {
        if (!isset($this->attributes[$key])) {
            return null;
        }

        return $this->attributes[$key];
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key) {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Update the current row in the database.
     * @param $values
     * @param \Closure $callback
     */
    public function update($values, $callback = null)
    {
        $query = app('database')->table($this->table)->where('id', $this->id);

        if ($callback && is_callable($callback)) {
            $query = $callback($query);
        }

        $query->update($values);
    }

    /**
     * Insert a new row into the database.
     * @param array $values
     * @param \Closure $callback
     */
    public static function insert(array $values, $callback = null)
    {
        $query = static::table();

        if ($callback && is_callable($callback)) {
            $query = $callback($query);
        }

        $query->insert($values);
    }

    /**
     * Find a model by id.
     * @param $id
     * @param \Closure $callback
     * @return mixed
     */
    public static function find($id, $callback = null)
    {
        $query = static::table()->where('id', $id);

        if ($callback && is_callable($callback)) {
            $query = $callback($query);
        }

        $data = $query->first();

        if (empty($data)) {
            return null;
        }

        return new static($data);
    }

    /**
     * Find a model by id or send a 404 response.
     * @param $id
     * @param \Closure $callback
     * @return mixed
     */
    public static function findOrFail($id, $callback = null)
    {
        $model = static::find($id, $callback);

        if (empty($model)) {
            app()->abort(404);
        }

        return $model;
    }

    /**
     * Easily instantiate a new where query on the model's table.
     * @param array ...$arguments
     * @return mixed
     */
    public static function where(...$arguments)
    {
        return static::table()->where(...$arguments);
    }

    /**
     * Get all records for the model.
     * @return mixed
     */
    public static function all()
    {
        return static::table()->get();
        
        return static::table()->get()->map(function ($model) {
            return [];
        })->all();
    }

    /**
     * Get a query with the model's table preselected.
     * @return mixed
     */
    public static function table()
    {
        $model = new static();

        return app('database')->table($model->table);
    }

}