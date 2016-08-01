<?php namespace MrCat\SuiteCrm\Model;

use MrCat\SuiteCrm\Http\Api;

class Bean
{

    /**
     * Get All Records.
     *
     * @param $module
     * @param array $options
     *
     * @return mixed
     */
    public static function all($module, array $options)
    {
        return Api::get()->getEntryList($module, $options);
    }

    /**
     * Find Entry Record.
     *
     * @param $module
     * @param $id
     *
     * @return array
     */
    public static function find($module, $id)
    {
        $data = Api::get()->getEntry($module, $id);

        return suite_params_array_response($data['entry_list'][0]);
    }

    /**
     * Update Records
     *
     * @param $module
     * @param $data
     *
     * @return array
     */
    public static function save($module, $data)
    {
        $data = Api::get()->setEntry($module, $data);

        if (array_key_exists('id', $data)) {
            return true;
        }

        return false;
    }

    /**
     * Create New Records.
     *
     * @param $module
     * @param array $data
     *
     * @return bool
     */
    public static function create($module, array $data)
    {
        $data = Api::get()->setEntries($module, $data);

        if (array_key_exists('ids', $data)) {
            return true;
        }

        return false;
    }
}
