<?php

namespace DezByte\Grid;

/**
 * http://site.com/products/grid-mapper/filter-size/eq-27-gt-19-lt-27/filter-vendor/eq-lg
 * http://site.com/products/filter/filter-vendor/eq-asus-samsung-dell-ne-acer/display-ge-1920-eq-128-192/order/product_id-desc
 *
 * Class Grid
 * @package DezByte\Grid
 */
class Grid
{
  
  const DEFAULT_MARKER = 'filter';

  const LIKE = 'lk';
  const NOT_LIKE = 'nl';
  const EQ = 'eq';
  const NE = 'ne';
  const GT = 'gt';
  const GE = 'ge';
  const LT = 'lt';
  const LE = 'le';
  
  /**
   * @var array
   */
  protected $allowFilterNames = [
    self::LIKE,
    self::NOT_LIKE,
    self::EQ,
    self::NE,
    self::GT,
    self::GE,
    self::LT,
    self::LE
  ];
  
  /**
   * @var string
   */
  protected $filterMarker = self::DEFAULT_MARKER;

  
  /**
   * @var array
   */
  protected $filters = [];
  
  /**
   * Grid constructor.
   */
  public function __construct()
  {
  }
  
  /**
   * @param $column
   * @param $filter
   * @param $value
   * @param bool $reset
   * @return string
   */
  public function filter($column, $value, $filter = Grid::EQ, $reset = false)
  {
    $rewrite = false === $reset
      ? ['filters' => $this->getFilters()] : [];
    
    $rewrite['filters'][$column][$filter][] = $value;
    
    $parameters = $this->getParameters($rewrite);
    
    $array = [];
    
    foreach ($parameters as $column => $filter) {
      $array[] = sprintf('%s/%s', $column, $filter);
    }
    
    return implode('/', $array);
  }
  
  /**
   * @param null $requestString
   * @return $this
   */
  public function processRequest($requestString = null)
  {
    $parameters = explode('/', trim($requestString, '/'));
    
    $index = array_search($this->getFilterMarker(), $parameters);
    
    if (false !== $index) {
      
      $pairs = array_chunk(array_slice($parameters, $index + 1), 2);
      
      foreach ($pairs as $pair) {
    
        list($column, $filters) = $pair;
  
        $filters = explode('-', $filters);
        
        $filterName = Grid::EQ;
        while ($filter = array_shift($filters)) {
          
          if (in_array($filter, $this->getAllowFilterNames(), true)) {
            $filterName = $filter; continue;
          }
  
          $this->addFilter($column, $filterName, $filter);
        }
        
      }
    }
    
    return $this;
  }
  
  /**
   * @param array $rewrite
   * @return array
   */
  public function getParameters(array $rewrite = [])
  {
    $rewrite['filters'] = isset($rewrite['filters']) ? $rewrite['filters'] : $this->getFilters();
  
    $parameters = [];
    foreach ($rewrite['filters'] as $columnName => $filters) {
      $columnFilter = [];
      
      foreach ($filters as $name => $filter) {
        $columnFilter[] = sprintf('%s-%s', $name, implode('-', $filter));
      }
      
      $parameters[$columnName] = implode('-', $columnFilter);
    }
    
    return $parameters;
  }
  
  /**
   * @param $column
   * @param $filterName
   * @param $filter
   * @return $this
   */
  public function addFilter($column, $filterName, $filter)
  {
    $this->filters[$column][$filterName][] = $filter;
    
    return $this;
  }
  
  
  /**
   * @return array
   */
  public function getFilters()
  {
    return $this->filters;
  }
  
  /**
   * @return array
   */
  public function getAllowFilterNames()
  {
    return $this->allowFilterNames;
  }
  
  /**
   * @return string
   */
  public function getFilterMarker()
  {
    return $this->filterMarker;
  }
  
  
}