<?php namespace MrCat\SuiteCrm\Model;

use MrCat\SuiteCrm\Form\Form;

abstract class SuiteCrm
{

    /**
     * Module Name SuiteCrm
     *
     * @var string
     */
    protected $module = '';

    /**
     * Select Fields for FORM.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The model's attributes.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Save a new model and return the instance.
     *
     * @param  array $attributes
     *
     * @return static
     */
    public static function create(array  $attributes)
    {
        $instance = new static();

        return Bean::create($instance->module, $attributes);
    }

    /**
     * Save the model in the database.
     *
     * @param  array $attributes
     *
     * @return bool|int
     */
    public function save(array $attributes = [])
    {
        return Bean::save($this->module, array_merge($this->attributes, $attributes));
    }

    /**
     * Execute a query for a single record by ID.
     *
     * @param  int $id
     *
     * @return mixed|static
     */
    public static function find($id)
    {
        $instance = new static();

        $data = Bean::find($instance->module, $id);

        $instance->makeAttributes($data);

        return $instance;
    }

    /**
     * Delete a record from the database.
     *
     * @return int
     */
    public function delete()
    {

    }

    /**
     * Generates the attributes of the User class
     *
     * @param $attributes
     */
    public function makeAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Instance Form.
     *
     *
     */
    public static function form()
    {
        $instance = new static();
        return new Form($instance->module, $instance->fields);
    }

    /**
     * SuiteCrm constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Dynamically retrieve attributes on the User model.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * Dynamically set attributes on the User model.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static();

        if ($method === 'select') {

            $builder = new Builder($instance->module);

            return call_user_func_array([$builder, $method], $parameters);
        }
    }
}
