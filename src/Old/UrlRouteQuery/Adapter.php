<?php

namespace Dez\Mvc\UrlRouteQuery;

abstract class Adapter
{

    /**
     * Adapter constructor.
     * @param null $source
     */
    public function __construct($source = null)
    {
        $this->setSourceData($source);
    }

    /**
     * @var array
     */
    static public $criteria = [
        Filter::FILTER_EQUAL => '=',
        Filter::FILTER_LIKE => 'LIKE',
        Filter::FILTER_NOT_LIKE => 'NOT LIKE',
        Filter::FILTER_GREATER_THAN => '>',
        Filter::FILTER_GREATER_THAN_EQUAL => '>=',
        Filter::FILTER_LESS_THAN => '<',
        Filter::FILTER_LESS_THAN_EQUAL => '<=',
        Filter::FILTER_NOT_EQUAL => '!=',
    ];

    /**
     * @param null $data
     * @return $this
     */
    abstract protected function setSourceData($data = null);

    /**
     * @param array $params
     * @return $this
     */
    abstract public function process(array $params = []);

    /**
     * @return mixed
     */
    abstract public function getData();

}