<?php namespace MrCat\SuiteCrm\Http;

class Api
{
    /**
     * New instance class.
     *
     * @var \App\SuiteCrm\Http\Api
     */
    private static $instance = null;

    /**
     * Parameters for Request.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Session Sugar application.
     *
     * @var string
     */
    private $session = '';

    /**
     * Gets the instance via lazy initialization (created on first usage).
     *
     * @return self
     */
    public static function get()
    {
        return self::$instance;
    }

    /**
     * Logs a user into the Sugar application.
     *
     * @param array $credentials
     *
     * @return mixed
     */
    public function login(array $credentials = [])
    {
        $this->parameters['form_params'] = [
            'user_auth'       => [
                'user_name' => $credentials['user'],
                'password'  => md5($credentials['password']),
            ],
            'name_value_list' => [
                [
                    'name'  => 'notifyonsave',
                    'value' => 'true',
                ],
            ],
        ];

        return Request::send('login', $this->parameters);
    }

    /**
     * Add new Session.
     *
     * @param $user
     * @param $password
     *
     * @return $this
     * @throws \Exception
     */
    public function addSession($user, $password)
    {
        $login = $this->login([
            'user'     => $user,
            'password' => $password,
        ]);

        $this->session = $this->validateSession($login);

        return $this;
    }

    /**
     * Set Attribute Session.
     *
     * @param $session
     *
     * @return $this
     */
    public function setSession($session)
    {
        if ($this->hasSession($session)) {

            $this->session = $session;

            return $this;
        }
    }

    /**
     * Isset Session Api.
     *
     * @param $session
     *
     * @return bool
     */
    public function hasSession($session)
    {
        if (is_null($session) || !is_string($this->issetSession($session))) {
            return false;
        }

        return true;
    }

    /**
     * Get Attribute Session.
     *
     * @return string
     * @throws \Exception
     */
    public function getSession()
    {
        if ($this->hasSession($this->session)) {
            return $this->session;
        }
    }

    /**
     * LogOut Api
     *
     * @return mixed
     * @throws \Exception
     */
    public function unsetSession()
    {
        $this->parameters['form_params'] = [
            'sesion' => $this->getSession(),
        ];

        $request = Request::send('logout', $this->parameters);

        $this->setSession($request);
    }

    /**
     * Retrieves the OAuth Access Token
     *
     * @param $session
     *
     * @return mixed
     * @throws \Exception
     */
    private function issetSession($session)
    {
        $this->parameters['form_params'] = [
            'session' => $session,
        ];

        $response = Request::send('oauth_access', $this->parameters);

        return $this->validateSession($response);
    }

    /**
     * Retrieves a single bean based on record ID.
     *
     *
     * @param $module
     * @param $id
     *
     * @return resource
     * @throws \Exception
     */
    public function getEntry($module, $id)
    {
        $this->parameters['form_params'] = [
            'session'                   => $this->getSession(),
            'module_name'               => $module,
            'id'                        => $id,
            'select_fields'             => [],
            'link_name_to_fields_array' => [],
        ];

        $response = Request::send('get_entry', $this->parameters);

        return $this->validateResponse($response);
    }

    /**
     * Creates or updates a specific record.
     *
     * @param string $module
     * @param array $data
     *
     * @return mixed
     *
     * @return resource
     */
    public function setEntry($module, array $data = [])
    {
        $this->parameters['form_params'] = [
            'session'         => $this->getSession(),
            'module_name'     => $module,
            'name_value_list' => suite_params_array_request(str_replace("&", "%26", $data)),
        ];

        $response = Request::send('set_entry', $this->parameters);

        return $this->validateResponse($response);
    }

    /**
     * Retrieves a list of beans based on query specifications.
     *
     * @param string $module
     * @param array $options
     *
     * @return mixed
     */
    public function getEntryList($module, array $options = [])
    {
        $this->parameters['form_params'] = [
            'session'                   => $this->getSession(),
            'module_name'               => $module,
            'query'                     => $options['query'],
            'order_by'                  => $options['order_by'],
            'offset'                    => $options['offset'],
            'select_fields'             => $options['select_fields'],
            'link_name_to_fields_array' => $options['link_name_to_fields_array'],
            'max_results'               => $options['max_results'],
            'deleted'                   => $options['deleted'],
        ];

        $response = Request::send('get_entry_list', $this->parameters);

        return $this->validateResponse($response);
    }

    /**
     * Retrieves a list of beans based on query specifications.
     *
     * @param string $module
     * @param array $data
     *
     * @return mixed
     */
    public function setEntries($module, array $data = [])
    {
        $this->parameters['form_params'] = [
            'session'         => $this->getSession(),
            'module_name'     => $module,
            'name_value_list' => suite_params_array_request_multiple(str_replace("&", "%26", $data)),
        ];

        $response = Request::send('set_entries', $this->parameters);

        return $this->validateResponse($response);
    }


    /**
     * Retrieves the list of field vardefs for a specific module.
     *
     * @param string $module
     *
     * @return mixed
     */
    public function getModuleFields($module)
    {
        $this->parameters['form_params'] = [
            'session'     => $this->getSession(),
            'module_name' => $module,
        ];

        $response = Request::send('get_module_fields', $this->parameters);

        return $this->validateResponse($response);
    }

    /**
     * Validate Session Key.
     *
     * @param array $session
     *
     * @return mixed
     * @throws \Exception
     */
    private function validateSession(array $session = [])
    {
        // is incorrect login
        if (array_key_exists('name', $session) && $session['name'] === "Invalid Login") {
            throw new \Exception($session['description']);
        }

        if (array_key_exists('id', $session)) {
            return $session['id'];
        }
    }

    /**
     * Validate Response Api.
     *
     * @param array $response
     *
     * @throws \Exception
     *
     * @return array
     */
    private function validateResponse(array $response = [])
    {
        // is module exists
        if (array_key_exists('name', $response) && $response['name'] == 'Module Does Not Exist') {
            throw new \Exception($response['description']);
        }

        // is exists record
        if (
            array_key_exists('entry_list', $response) &&
            array_key_exists('0', $response['entry_list']) &&
            array_key_exists('name_value_list', $response['entry_list'][0]) &&
            array_key_exists('0', $response['entry_list'][0]['name_value_list']) &&
            array_key_exists('name', $response['entry_list'][0]['name_value_list'][0]) &&
            is_string($response['entry_list'][0]['name_value_list'][0]['name']) &&
            $response['entry_list'][0]['name_value_list'][0]['name'] === 'warning'
        ) {
            throw new \Exception($response['entry_list'][0]['name_value_list'][0]['value']);
        }

        return $response;
    }

    /**
     * Instance new Api with config.
     *
     * @param array $parameters
     *
     * @return static
     */
    public static function config(array $parameters)
    {
        if (null === static::$instance) {
            static::$instance = new static($parameters);
        }

        return static::$instance;
    }

    /**
     * Api constructor.
     *
     * @param array $parameters
     */
    private function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Prevent the instance from being cloned.
     *
     * @throws \Exception
     *
     * @return void
     */
    final public function __clone()
    {
        throw new \Exception('This is a Singleton. Clone is forbidden');
    }

    /**
     * Prevent from being unserialized.
     *
     * @throws \Exception
     *
     * @return void
     */
    final public function __wakeup()
    {
        throw new \Exception('This is a Singleton. __wakeup usage is forbidden');
    }
}
