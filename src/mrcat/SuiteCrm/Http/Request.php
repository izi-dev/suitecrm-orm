<?php namespace MrCat\SuiteCrm\Http;

use GuzzleHttp\Client;

class Request
{
    /**
     * Request parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Instance new Client.
     *
     * @return \GuzzleHttp\Client
     */
    public function newClient()
    {
        return new Client([
            'base_uri' => $this->parameters['base_uri'],
            'timeout'  => $this->parameters['timeout'],
        ]);
    }

    /**
     * Request Api.
     *
     * @param $method
     *
     * @return string JSON
     */
    public function call($method)
    {
        $response = $this->newClient()->request($this->parameters['method'], $this->parameters['uri'], [
            'form_params' => [
                'method'        => $method,
                'input_type'    => 'JSON',
                'response_type' => 'JSON',
                'rest_data'     => json_encode($this->parameters['form_params']),
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Instance New Request.
     *
     * @param $method
     * @param array $parameters
     *
     * @return array
     */
    public static function send($method, array $parameters)
    {
        $instance = new static($parameters);

        return $instance->call($method);
    }

    /**
     * Validates the keys of the parameters.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        if (!array_key_exists('timeout', $this->parameters) || !is_float($this->parameters['timeout'])) {
            $this->parameters['timeout'] = 2.0;
        }

        if (!array_key_exists('method', $this->parameters) || !is_string($this->parameters['method'])) {
            $this->parameters['method'] = 'POST';
        }

        if (!array_key_exists('base_uri', $this->parameters) || !is_string($this->parameters['base_uri'])) {
            $this->parameters['base_uri'] = '';
        }

        if (!array_key_exists('uri', $this->parameters) || !is_string($this->parameters['uri'])) {
            $this->parameters['uri'] = '';
        }

        if (!array_key_exists('form_params', $this->parameters) || !is_array($this->parameters['form_params'])) {
            $this->parameters['form_params'] = [];
        }
    }

    /**
     * Request constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->setParameters($parameters);
    }
}
