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
  
  const RESET_ALL = 1;
  const RESET_COLUMN = 2;
  const RESET_VALUE = 4;
  
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
  protected $prefixPath;
  
  /**
   * @var Url
   */
  protected $url;
  
  /**
   * @var array
   */
  protected $filters = [];
  
  /**
   * @var string
   */
  protected $filterMarker = self::DEFAULT_MARKER;
  
  /**
   * Grid constructor.
   * @param Url $url
   */
  public function __construct(Url $url)
  {
    $this->url = $url;
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
            $filterName = $filter;
            continue;
          }
          
          $this->addFilter($column, $filterName, $filter);
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
  
  /**
   * @param string $filterMarker
   */
  public function setFilterMarker($filterMarker)
  {
    $this->filterMarker = $filterMarker;
  }
  
  /**
   * @return string
   */
  public function getPrefixPath()
  {
    return $this->prefixPath;
  }
  
  /**
   * @param string $prefixPath
   */
  public function setPrefixPath($prefixPath)
  {
    $this->prefixPath = $prefixPath;
  }
  
  /**
   * @return Url
   */
  public function getUrl()
  {
    return $this->url;
  }
  
  /**
   * @param Url $url
   */
  public function setUrl(Url $url)
  {
    $this->url = $url;
  }
  
  /**
   * @return GridFilter
   */
  public function getFilter()
  {
    return new GridFilter($this);
  }
  
  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getFilter()->render();
  }
  
}