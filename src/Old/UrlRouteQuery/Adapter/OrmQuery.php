<?php

namespace Dez\Mvc\UrlRouteQuery\Adapter;

use Dez\Mvc\UrlRouteQuery\Mapper;
use Dez\Mvc\UrlRouteQuery\MapperException;
use Dez\Mvc\UrlRouteQuery\Adapter;
use Dez\ORM\Model\QueryBuilder;

class OrmQuery extends Adapter {

    /**
     * @var QueryBuilder
     */
    protected $query;

    /**
     * @param QueryBuilder $query
     * @return $this
     * @throws MapperException
     */
    protected function setSourceData($query = null)
    {
        if(! ($query instanceof QueryBuilder)) {
            throw new MapperException("Source must be instance of ORM Table");
        }
        
        $this->query = $query;

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function process(array $params = [])
    {

        $filter = $params['filter'];
        $order = $params['order'];

        if(count($filter) > 0) {
            foreach($filter as $column => $conditions) {
                foreach($conditions as $criterion => $value) {
                    if(Mapper::MAPPER_LIKE === $criterion || Mapper::MAPPER_NOT_LIKE === $criterion) {
                        $value = addslashes(preg_replace('/[^а-яa-z0-9_\.]+/ui', '', $value));
                        $criterion = static::$criteria[$criterion];
                        $this->query->whereRaw("`{$column}` {$criterion} '%{$value}%'");
                    } else {
                        $this->query->where($column, $value, static::$criteria[$criterion]);
                    }
                }
            }
        }

        if(count($order) > 0) {
            foreach($order as $column => $vector) {
                $this->query->order($column, $vector);
            }
        }

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function getData()
    {
        return $this->query;
    }


}