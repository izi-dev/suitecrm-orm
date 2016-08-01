<?php namespace MrCat\SuiteCrm\Model;

class Builder
{
    /**
     * Name Module Suite
     *
     * @var string
     */
    protected $module = '';

    /**
     * Option get Entries Api.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Response data Api.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The list of fields to be returned in the results.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function select(array $fields = [])
    {
        $this->options['select_fields'] = $fields;

        return $this;
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int $page
     * @param int $limit
     *
     * @return $this
     */
    public function paginate($page = 0, $limit = 15)
    {
        $this->max($limit);
        $this->offset($this->setPage($page, $limit));

        return $this;
    }

    /**
     * Calculate Offset.
     *
     * @param $page
     * @param $limit
     *
     * @return int
     */
    private function setPage($page, $limit)
    {
        if (isset($page) && !is_null($page) && $page > 1) {

            $page = $page - 1;

            return $page * $limit;
        }
    }

    /**
     * Contain relations
     *
     * @param array $relations
     *
     * @return $this
     */
    public function contain(array $relations = [])
    {
        $this->options['link_name_to_fields_array'] = $relations;

        return $this;
    }

    /**
     * Order query field
     *
     * @param string $field
     *
     * @return $this
     */
    public function orderBy($field = '')
    {
        $this->options['order_by'] = $field;

        return $this;
    }

    /**
     * The record offset from which to start.
     *
     * @param int $offset
     *
     * @return $this
     */
    public function offset($offset = 0)
    {
        $this->options['offset'] = $offset;

        return $this;
    }

    /**
     * The maximum number of results to return.
     *
     * @param int $max
     *
     * @return $this
     */
    public function max($max = 0)
    {
        $this->options['max_results'] = $max;

        return $this;
    }

    /**
     * If deleted records should be included in the results.
     *
     * @param $bool
     *
     * @return $this
     */
    public function withDeleted($bool = false)
    {
        $this->options['deleted'] = $bool;

        return $this;
    }

    /**
     * Page prev.
     *
     * @return float|int|null
     */
    private function paginationPrev()
    {
        $page = $this->paginationCurrent() - 1;

        return $page <= 0 ? null : $page;
    }

    /**
     * Page Current.
     *
     * @return float|int
     */
    private function paginationCurrent()
    {
        if ($this->options['offset'] == 0 || $this->options['max_results'] == 0) {
            return 1;
        }

        return round($this->options['offset'] / $this->options['max_results']);
    }

    /**
     * Page Next.
     *
     * @return float|int|null
     */
    private function paginationNext()
    {
        $page = $this->paginationCurrent() + 1;

        return $page >= $this->paginationLast() ? null : $page;
    }

    /**
     * Page Last.
     *
     * @return float|int
     */
    private function paginationLast()
    {
        if ($this->data['total_count'] == 0 || $this->options['max_results'] == 0) {
            return 1;
        }

        if ($this->data['total_count'] <= $this->options['max_results']) {
            return 1;
        }

        return round($this->data['total_count'] / $this->options['max_results']);
    }

    /**
     * The SQL WHERE clause without the word "where". You should remember to specify the table name for the fields to
     * avoid any ambiguous column errors.
     *
     * @param string $query
     *
     * @return $this
     */
    public function where($query = '')
    {
        $this->options['query'] = $query;

        return $this;
    }

    /**
     * Get Records All.
     *
     * @return array
     */
    public function get()
    {
        $this->data = Bean::all($this->module, $this->options);

        return [
            'data'     => suite_params_array_response_multiple($this->data['entry_list']),
            'paginate' => [
                'count' => (int)$this->data['result_count'],
                'total' => (int)$this->data['total_count'],
                'first' => 1,
                'last'  => $this->paginationLast(),
                'prev'  => $this->paginationPrev(),
                'next'  => $this->paginationNext(),
                'limit' => (int)$this->options['max_results'],
            ],
        ];
    }

    /**
     * SuiteCrm constructor.
     *
     * @param $module
     */
    public function __construct($module)
    {
        $this->module = $module;
        $this->where();
        $this->orderBy();
        $this->offset();
        $this->contain();
        $this->max();
        $this->withDeleted();
    }
}
