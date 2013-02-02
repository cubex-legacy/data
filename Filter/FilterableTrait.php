<?php
/**
 * @author  brooke.bryan
 */

namespace Cubex\Data\Filter;

trait FilterableTrait
{
  protected $_filters = [];

  public function addFilter($filter, array $options = [], $alias = null)
  {
    $append = array('filter' => $filter, 'options' => $options);

    if($alias === null)
    {
      $this->_filters[] = $append;
    }
    else
    {
      $this->_filters[$alias] = $append;
    }
    return $this;
  }

  public function filterExists($identify)
  {
    if(isset($this->_filters[$identify]))
    {
      return true;
    }
    else
    {
      foreach($this->_filters as $filter)
      {
        if($identify === $filter['filter'])
        {
          return true;
        }
      }
    }
    return false;
  }

  public function setFilters($filters)
  {
    $this->_filters = $filters;
    return $this;
  }

  public function getFilters()
  {
    return $this->_filters;
  }

  public function removeFilterByAlias($alias)
  {
    unset($this->_filters[$alias]);
    return $this;
  }

  public function removeFilter($filter)
  {
    $key = array_search($filter, $this->_filters);
    if($key !== null)
    {
      unset($this->_filters[$key]);
    }
    return $this;
  }

  public function removeAllFilters()
  {
    $this->_filters = [];
    return $this;
  }

  public function filter($value)
  {
    foreach($this->_filters as $filterable)
    {
      $filter  = $filterable['filter'];
      $options = (array)$filterable['options'];

      if(is_callable($filter))
      {
        $params = $options;
        array_unshift($params, $value);
        $value = call_user_func_array($filter, $params);
        continue;
      }
      else if(is_scalar($filter))
      {
        if(class_exists($filter))
        {
          $filter = new $filter();
        }
      }

      if($filter instanceof FilterInterface)
      {
        $filter->setOptions($options);
        $value = $filter->filter($value);
      }
    }
    return $value;
  }
}
