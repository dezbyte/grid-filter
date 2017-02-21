<?php

namespace Dez\Mvc\UrlRouteQuery;

class Order implements \IteratorAggregate, \JsonSerializable {

    const ORDER_DESC = 'desc';
    const ORDER_ASC = 'asc';

    /**
     * @var array
     */
    protected $orders = [];

    /**
     * @var Mapper
     */
    protected $mapper = null;

    /**
     * Order constructor.
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return Mapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->orders);
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return json_encode($this->orders, JSON_PRETTY_PRINT);
    }


}