<?php

namespace DezByte\Grid;

use Dez\Url\Url;

/**
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
  
  protected $url;
  
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
   * @param Url $url
   */
  public function __construct(Url $url)
  {
    $this->url = $url;
  }

  /**
   * @param $column
   * @param $value
   * @param $filter
   * @param bool $reset
   * @return string
   */
  public function filter($column, $value, $filter = Grid::EQ, $reset = false)
  {
    $rewrite = false === $reset ? $this->getFilters() : [];
    
    $rewrite[$column][$filter][] = $value;
    
    $parameters = $this->getParameters($rewrite);
    
    $filters = [];
    foreach ($parameters as $column => $filter) {
      $filters[] = sprintf('%s/%s', $column, $filter);
    }
    
    return $this->url->path(sprintf('%s/%s', $this->getFilterMarker(), implode('/', $filters)));
  }
  
  /**
   * @param null $requestString
   * @return $this
   */
  public function processRequest($requestString = null)
  {
    // cleanup request string
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
    $parameters = [];
    
    foreach ($rewrite as $columnName => $filters) {
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