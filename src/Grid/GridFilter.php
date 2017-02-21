<?php

namespace DezByte\Grid;

/**
 * Class GridFilter
 *
 * @package DezByte\Grid
 */
class GridFilter
{
  
  /**
   * @var array
   */
  protected $parameters = [];
  
  /**
   * @var array
   */
  protected $filters = [];
  
  /**
   * @var Grid
   */
  protected $grid;
  
  /**
   * GridFilter constructor.
   * @param Grid $grid
   */
  public function __construct(Grid $grid)
  {
    $this->grid = $grid;
    $this->filters = $grid->getFilters();
  }
  
  
  /**
   * @param string $column
   * @param mixed $value
   * @param string $filter
   * @return string
   */
  public function filter($column, $value, $filter = Grid::EQ)
  {
    $this->filters[$column][$filter][] = $value;

    return $this;
  }
  
  /**
   * @param int $reset
   * @param null $column
   * @param null $value
   * @return $this
   */
  public function reset($reset = Grid::RESET_ALL, $column = null, $value = null)
  {
    switch ($reset) {
      
      case Grid::RESET_ALL:
        $this->filters = [];
        break;
      
      case Grid::RESET_COLUMN:
        unset($this->filters[$column]);
        break;
      
      case Grid::RESET_VALUE:
        foreach ($this->filters[$column] as $filterName => $columnFilters) {
          if (false !== ($index = array_search($value, $columnFilters))) {
            unset($this->filters[$column][$filterName][$index]);
          }
        }
        break;
    }
    
    return $this;
  }
  
  /**
   * @return $this
   */
  public function prepareParameters()
  {
    $this->parameters = [];
    
    foreach ($this->getFilters() as $columnName => $filters) {
      $columnFilter = [];
      
      foreach ($filters as $name => $filter) {
        if (count($filter) > 0) {
          $columnFilter[] = sprintf('%s-%s', $name, implode('-', $filter));
        }
      }
      
      $this->parameters[$columnName] = implode('-', $columnFilter);
    }
    
    return $this;
  }
  
  /**
   * @return string
   */
  public function render()
  {
    $columnFilters = [];
  
    $this->prepareParameters();
    
    foreach ($this->getParameters() as $column => $filter) {
      $columnFilters[] = sprintf('%s/%s', $column, $filter);
    }
    
    $grid = $this->getGrid();

    $path = $this->hasFilters()
      ? sprintf('%s/%s/%s', $grid->getPrefixPath(), $grid->getFilterMarker(), implode('/', $columnFilters))
      : $grid->getPrefixPath();
    
    return $grid->getUrl()->path($path);
  }
  
  /**
   * @return array
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  
  /**
   * @return array
   */
  public function getFilters()
  {
    return $this->filters;
  }
  
  /**
   * @return bool
   */
  public function hasFilters()
  {
    return count($this->filters) > 0;
  }
  
  /**
   * @return Grid
   */
  public function getGrid()
  {
    return $this->grid;
  }
  
  /**
   * @return string
   */
  public function __toString()
  {
    return $this->render();
  }
  
}