<?php

if (!function_exists('suite_params_array_request')) {
    /**
     * Converts a SuiteCrm-REST compatible name_value to an Array
     *
     * @param array $data
     *
     * @return array
     */
    function suite_params_array_request(array $data = [])
    {
        $return = [];
        foreach ($data as $key => $value) {
            $return[] = ['name' => $key, 'value' => $value];
        }

        return $return;
    }
}

if (!function_exists('suite_params_array_request_multiple')) {
    /**
     * Converts a SuiteCrm-REST compatible name_value to an Array
     *
     * @param array $data
     *
     * @return array
     */
    function suite_params_array_request_multiple(array $data = [])
    {
        $return = [];
        foreach ($data as $keyData => $valueData) {
            foreach ($valueData as $key => $value) {
                $return[] = [
                    'name'  => $key,
                    'value' => $value,
                ];
            }
        }

        return $return;
    }
}

if (!function_exists('suite_params_array_response')) {
    /**
     *  Converts a SuiteCrm-REST compatible name_value_list to an Array
     *
     * @param array $data
     *
     * @return array
     */
    function suite_params_array_response(array $data = [])
    {
        $return = [];
        foreach ($data['name_value_list'] as $row) {
            $return[$row['name']] = $row['value'];
        }

        return $return;
    }
}

if (!function_exists('suite_params_array_response_multiple')) {
    /**
     *  Converts a SuiteCrm-REST compatible name_value_list to an Array
     *
     * @param array $data
     *
     * @return array
     */
    function suite_params_array_response_multiple(array $data = [])
    {
        $return = [];
        foreach ($data as $keyData => $valueData) {
            foreach ($valueData['name_value_list'] as $value) {
                $return[$keyData][$value['name']] = $value['value'];
            }
        }

        return $return;
    }
}