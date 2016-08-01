<?php namespace MrCat\SuiteCrm\Form;

use MrCat\SuiteCrm\Http\Api;

class Form
{
    /**
     * Rules Fields.
     *
     * @var array
     */
    private $fieldsRules = [
        'varchar' => 'alpha_num',
        'text'    => 'alpha_num',
        'url'     => 'url',
        'email'   => 'email',
        'phone'   => 'number',
        'int'     => 'number',
    ];

    /**
     * Get Select Fields Form
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Name Module Api.
     *
     * @var string
     */
    protected $module = '';

    /**
     * Form constructor.
     *
     * @param $module
     * @param array $fields
     */
    public function __construct($module, array $fields = [])
    {
        $this->fields = $fields;
        $this->module = $module;
    }

    /**
     * Select Fields Array
     *
     * @return array
     */
    private function selectFields()
    {
        $data = Api::get()->getModuleFields($this->module);

        return array_filter($data['module_fields'], function ($key) {
            return in_array($key, $this->fields);
        },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Generarte Form.
     *
     * @param array $attributes
     *
     * @param array $defaults
     *
     * @return array
     */
    public function generateFields(array $attributes = [], $defaults = [])
    {
        $data = [];
        foreach ($this->selectFields() as $key => $value) {
            $data[$key] = [
                'label' => $value['label'],
                'field' => GenerateHtml::input(
                    $value['type'],
                    [
                        'name'       => $value['name'],
                        'attributes' => $this->setAttributesForm($value['required'], $attributes),
                        'default'    => $this->generateValuesDefault($value,$defaults),
                        'options'    => $this->getOptionsForm($value['options']),
                    ]),
            ];
        }

        return $data;
    }

    /**
     * Generate Values Fields Form.
     *
     * @param $value
     * @param array $default
     *
     * @return mixed|null
     */
    private function generateValuesDefault($value, $default = [])
    {
        if (count($default) > 0) {
            return $default[$value['name']];
        }

        return isset($value['default']) ? $value['default'] : null;
    }

    /**
     * Genarete Options for Input Multiple
     *
     * @param $options
     *
     * @return array
     */
    private function getOptionsForm($options)
    {
        $data = [];
        foreach ($options as $key => $value) {
            $data[$value['name']] = $value['value'];
        }

        return $data;
    }

    /**
     * Attributes For Input
     *
     * @param $required
     * @param $attributes
     *
     * @return array
     */
    private function setAttributesForm($required, $attributes)
    {
        $data = [];

        if ($required == 1) {
            $data = [
                'required' => 'required',
            ];
        }

        return array_merge($attributes, $data);
    }

    /**
     * Generate Rules For Create Records
     *
     * @return array
     */
    public function rules()
    {
        $fields = [];
        foreach ($this->selectFields() as $key => $value) {
            if ($value['type'] !== 'id') {
                $fields[$key] = [
                    $this->isRequiredField($value['required']),
                    key_exists($value['type'], $this->fieldsRules) ? $this->fieldsRules[$value['type']] : '',
                ];
            }
        }

        return $fields;
    }

    /**
     * Validate Field Required Rule
     *
     * @param $field
     *
     * @return string
     */
    private function isRequiredField($field)
    {
        if ($field == 1) {
            return 'required';
        }

        return '';
    }
}
