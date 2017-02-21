<?php

namespace Dez\Mvc\UrlRouteQuery;

use Dez\DependencyInjection\Injectable;
use Dez\Http\Request;
use Dez\Router\Router;
use Dez\Url\Url;

abstract class Mapper extends Injectable
{

    const MAPPER_IDENTITY = 'grid-mapper';

    /**
     * @var null
     */
    protected $dataSource = null;

    /**
     * @var array
     */
    protected $allowedFilter = [];

    /**
     * @var array
     */
    protected $allowedOrder = [];

    /**
     * @var null
     */
    protected $uniqueIdentity = null;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var array
     */
    protected $order = [];

    /**
     * @var string
     */
    protected $rootUrlPath = '/';

    /**
     * @var array
     */
    protected $toBuild = [
        'filter' => [],
        'order' => [],
    ];

    /**
     * Mapper constructor.
     */
    public function __construct()
    {
        if ($this->uniqueIdentity !== null) {
            $this->setPrefix("{$this->getUniqueIdentity()}-");
        }
    }

    /**
     * @return array
     */
    public function & getToBuild()
    {
        return $this->toBuild;
    }

    /**
     * @param array $toBuild
     * @return $this
     */
    public function toBuild(array $toBuild = [])
    {
        $this->toBuild = $toBuild;

        return $this;
    }

    /**
     * @return null
     */
    public function getUniqueIdentity()
    {
        return $this->uniqueIdentity;
    }

    /**
     * @param null $uniqueIdentity
     * @return static
     */
    public function setUniqueIdentity($uniqueIdentity)
    {
        $this->uniqueIdentity = $uniqueIdentity;

        if ($this->uniqueIdentity !== null) {
            $this->setPrefix("{$this->getUniqueIdentity()}-");
        }

        return $this;
    }

    /**
     * @throws MapperException
     */
    public function processRequestParams()
    {
        /** @var Request $request */
        $request = $this->getDi()->get('request');
        /** @var Router $router */
        $router = $this->getDi()->get('router');

        if (null === $request || null === $router) {
            throw new MapperException("Request or Router is required for Grid Mapper");
        }

        $dirtyMatches = $router->getDirtyMatches();

        $index = array_search(Mapper::MAPPER_IDENTITY, $dirtyMatches);

        if ($index !== false) {

            $matches = array_slice($dirtyMatches, $index + 1);
            $matches = array_column(array_chunk($matches, 2), 1, 0);

            foreach ($this->getAllowedFilter() as $filterColumn) {

                $key = $this->getPrefix() . 'filter-' . $filterColumn;
                $conditions = $request->getFromArray($matches, $key, null);

                if (null !== $conditions) {
                    $conditions = array_chunk(explode('-', $conditions), 2);
                    $conditions = array_column($conditions, 1, 0);

                    foreach ($conditions as $criterion => $value) {
                        if ($this->checkFilterCriterion($criterion)) {
                            $this->setFilter($filterColumn, $criterion, $value);
                        }
                    }
                }
            }

            foreach ($this->getAllowedOrder() as $orderColumn) {

                $key = $this->getPrefix() . 'order-' . $orderColumn;
                $founded = $request->getFromArray($matches, $key, null);

                if (null !== $founded) {
                    $this->setOrder($orderColumn, $founded);
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedFilter()
    {
        return $this->allowedFilter;
    }

    /**
     * @param array $allowedFilter
     * @return static
     */
    public function setAllowedFilter(array $allowedFilter = [])
    {
        $this->allowedFilter = $allowedFilter;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return static
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param string $criterion
     * @return mixed
     */
    public function checkFilterCriterion($criterion = Mapper::MAPPER_EQUAL)
    {
        return in_array($criterion, $this->getFilterCriteria(), true);
    }

    /**
     * @return array
     */
    public function getFilterCriteria()
    {
        return [
            Filter::FILTER_EQUAL,
            Filter::FILTER_NOT_EQUAL,
            Filter::FILTER_GREATER_THAN,
            Filter::FILTER_GREATER_THAN_EQUAL,
            Filter::FILTER_LESS_THAN,
            Filter::FILTER_LESS_THAN_EQUAL,
            Filter::FILTER_LIKE,
            Filter::FILTER_NOT_LIKE,
            Filter::FILTER_NULL,
        ];
    }

    /**
     * @return array
     */
    public function getAllowedOrder()
    {
        return $this->allowedOrder;
    }

    /**
     * @param array $allowedOrder
     * @return static
     */
    public function setAllowedOrder(array $allowedOrder = [])
    {
        $this->allowedOrder = $allowedOrder;

        return $this;
    }

    /**
     * @return mixed
     * @throws MapperException
     */
    public function processDataSource()
    {
        if (!($this->dataSource instanceof Adapter)) {
            throw new MapperException("Initialize before data source adapter");
        }

        $dataSourceParams = [
            'filter' => $this->getFilter(),
            'order' => $this->getOrder(),
        ];

        $this->getDataSource()->process($dataSourceParams);

        return $this->getDataSource()->getData();
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param $column
     * @param string $criterion
     * @param null $value
     * @return Mapper
     */
    public function setFilter($column, $criterion = Filter::FILTER_EQUAL, $value = null)
    {
        if ($this->checkFilterCriterion($criterion)) {
            $this->filter[$column][$criterion] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param $column
     * @param string $vector
     * @return static
     */
    public function setOrder($column, $vector = Mapper::MAPPER_ORDER_ASC)
    {
        $this->order[$column] = $vector;

        return $this;
    }

    /**
     * @return Adapter
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param Adapter $dataSource
     */
    public function setDataSource(Adapter $dataSource = null)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return string
     */
    public function getRootUrlPath()
    {
        return $this->rootUrlPath;
    }

    /**
     * @param string $rootUrlPath
     * @return static
     */
    public function setRootUrlPath($rootUrlPath)
    {
        $this->rootUrlPath = $rootUrlPath;

        return $this;
    }

    /**
     * @param string $path
     * @return Mapper
     */
    public function path($path = '/')
    {
        return $this->setRootUrlPath($path);
    }

    /**
     * @return string
     */
    public function url()
    {

        $path = $this->getRootUrlPath();

        if ($this->hasRequestRoute()) {

            $parameters = $this->toBuild;
            $urlPartials = [];

            if(isset($parameters['filter']) && count($parameters['filter']) > 0)
                foreach ($parameters['filter'] as $column => $filter) {
                    $urlPartials[] = "{$this->getPrefix()}filter-{$column}";
                    $filterConditions = [];

                    foreach ($filter as $criterion => $value) {
                        $filterConditions[] = "{$criterion}-{$value}";
                    }

                    $urlPartials[] = implode('-', $filterConditions);
                }

            if(isset($parameters['order']) && count($parameters['order']) > 0)
                foreach ($parameters['order'] as $column => $vector) {
                    $urlPartials[] = "{$this->getPrefix()}order-{$column}/{$vector}";
                }

            $filterRoute = implode('/', $urlPartials);

            $identity = Mapper::MAPPER_IDENTITY;

            $path = "{$path}/{$identity}/{$filterRoute}";
        }

        /** @var Url $url */
        $url = $this->getDi()->get('url');

        return $url->path($path);
    }

    /**
     * @return boolean
     */
    public function hasRequestRoute()
    {
        return (count($this->toBuild) !== count($this->toBuild, true));
    }

    /**
     * @return Order
     */
    public function order()
    {
        return new Order($this);
    }

    /**
     * @return Filter
     */
    public function filter()
    {
        return new Filter($this);
    }

    /**
     * @param $column
     * @param $value
     * @param string $criterion
     * @return bool
     */
    public function has($column, $value, $criterion = Filter::FILTER_EQUAL)
    {
        $filter = $this->getFilter();

        return isset($filter[$column], $filter[$column][$criterion]) && $filter[$column][$criterion] == $value;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->getDataSource()->getData();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->url();
    }

}