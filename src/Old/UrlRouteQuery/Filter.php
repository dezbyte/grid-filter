<?php

namespace Dez\Mvc\UrlRouteQuery;

class Filter implements \JsonSerializable {

    const FILTER_NULL = 'null';

    const FILTER_LIKE = 'lk';
    const FILTER_NOT_LIKE = 'nl';
    const FILTER_EQUAL = 'eq';
    const FILTER_NOT_EQUAL = 'ne';
    const FILTER_GREATER_THAN = 'gt';
    const FILTER_GREATER_THAN_EQUAL = 'ge';
    const FILTER_LESS_THAN = 'lt';
    const FILTER_LESS_THAN_EQUAL = 'le';

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var Mapper
     */
    protected $mapper = null;

    /**
     * Filter constructor.
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
        $this->mountFromTemporaryFilter();
    }

    /**
     * @return Filter
     */
    public function mountFromTemporaryFilter()
    {
        $mapperParams = &$this->getMapper()->getToBuild();

        $this->filters = &$mapperParams['filter'];

        return $this;
    }

    /**
     * @return Filter
     */
    public function mountFromRequestedFilter()
    {
        $this->getMapper()->toBuild(['filter' => $this->getMapper()->getFilter()]);

        return $this->mountFromTemporaryFilter();
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->filters = [];

        return $this;
    }

    /**
     * @param $column
     * @param $value
     * @param string $criterion
     * @return $this
     */
    public function attach($column, $value, $criterion = Filter::FILTER_EQUAL)
    {
        $this->filters[$column][$criterion] = $value;

        return $this;
    }

    /**
     * @param $column
     * @param $value
     * @param string $criterion
     * @return $this
     */
    public function set($column, $value, $criterion = Filter::FILTER_EQUAL)
    {
        return $this->reset()->attach($column, $value, $criterion);
    }

    /**
     * @param $column
     * @param $value
     * @param string $criterion
     * @return Filter
     */
    public function leave($column, $value, $criterion = Filter::FILTER_EQUAL)
    {
        $this->reset();
        $this->mountFromRequestedFilter();

        return $this->attach($column, $value, $criterion);
    }

    /**
     * @return Mapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return json_encode($this->filters, JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMapper()->url();
    }

}