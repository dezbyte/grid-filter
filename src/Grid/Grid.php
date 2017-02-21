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

  const FILTER_LIKE = 'lk';
  const FILTER_NOT_LIKE = 'nl';
  const FILTER_EQ = 'eq';
  const FILTER_NE = 'ne';
  const FILTER_GT = 'gt';
  const FILTER_GE = 'ge';
  const FILTER_LT = 'lt';
  const FILTER_LE = 'le';
  
  /**
   * @var array
   */
  protected $allowFilterNames = [
    self::FILTER_LIKE,
    self::FILTER_NOT_LIKE,
    self::FILTER_EQ,
    self::FILTER_NE,
    self::FILTER_GT,
    self::FILTER_GE,
    self::FILTER_LT,
    self::FILTER_LE
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
   * @param null $requestString
   * @return $this
   */
  public function processRequest($requestString = null)
  {
    $pairs = explode('/', trim($requestString, '/'));
    
    $index = array_search($this->getFilterMarker(), $pairs);
    
    if (false !== $index) {
      
      $pairs = array_chunk(array_slice($pairs, $index + 1), 2);
      
      foreach ($pairs as $pair) {
    
        list($column, $filters) = $pair;
  
        $filters = explode('-', $filters);
        
        $filterName = Grid::FILTER_EQ;
        while ($filter = array_shift($filters)) {
          if (in_array($filter, $this->getAllowFilterNames(), true)) {
            $filterName = $filter;
          } else {
            $this->addFilter($column, $filterName, $filter);
          }
        }
        
      }
    }
    
    
    return $this;
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
   * @param $column
   * @param null $filter
   * @return mixed|null
   */
  public function getFilter($column, $filter = null)
  {
    if (null === $filter) {
      return isset($this->filters[$column]) ? $this->filters[$column] : null;
    }
    
    return isset($this->filters[$column][$filter]) ? $this->filters[$column][$filter] : null;
  }
  
  /**
   * @param $column
   * @param null $filter
   * @return $this
   */
  public function removeFilter($column, $filter = null)
  {
    if (null === $filter && isset($this->filters[$column])) {
      unset($this->filters[$column]);
    } elseif (isset($this->filters[$column], $this->filters[$column][$filter])) {
      unset($this->filters[$column][$filter]);
    }
  
    return $this;
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