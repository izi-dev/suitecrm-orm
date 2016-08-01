<?php namespace MrCat\SuiteCrm\Form;

use MrCat\GenerateForm\Html as HTML;

class GenerateHtml
{
    protected $types = [
        'varchar'   => 'text',
        'text'      => 'textArea',
        'date'      => 'date',
        'enum'      => 'select',
        'multienum' => 'multiSelect',
        'radioenum' => 'select',
        'phone'     => 'number',
        'int'       => 'number',
        'name'      => 'text',
        'url'       => 'text',
    ];

    protected $options = [];


    /**
     * GenerateHtml constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function text()
    {
        return HTML::text(
            $this->options['name'],
            $this->options['default'],
            $this->options['attributes']
        );
    }

    public function select()
    {
        return HTML::select(
            $this->options['name'],
            $this->options['options'],
            $this->options['default'],
            $this->options['attributes']
        );
    }

    public function multiSelect()
    {
        $multiSelect = [
            'multiple' => 'multiple',
        ];

        return HTML::select(
            $this->options['name'],
            $this->options['options'],
            $this->options['default'],
            array_merge($multiSelect, $this->options['attributes'])
        );
    }

    public function textArea()
    {
        return HTML::textArea(
            $this->options['name'],
            $this->options['default'],
            $this->options['attributes']
        );
    }

    public function date()
    {
        return HTML::input(
            'date',
            $this->options['name'],
            $this->options['default'],
            $this->options['attributes']
        );
    }

    public function radio()
    {
        return HTML::radio(
            $this->options['name'],
            $this->options['default'],
            $this->options['attributes']
        );
    }

    public function number()
    {
        return HTML::input('number',
            $this->options['name'],
            $this->options['default'],
            $this->options['attributes']
        );
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $method = $this->types[$method];

        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     */
    public static function input($method, $parameters)
    {
        $instance = new static($parameters);

        return call_user_func_array([$instance, $method], $parameters);
    }
}
