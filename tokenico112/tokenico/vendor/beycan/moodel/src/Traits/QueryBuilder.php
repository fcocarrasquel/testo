<?php

namespace Beycan\Moodel\Traits;

trait QueryBuilder
{
    private $queryText = null;

    private $parameters = [];

    protected function createQuery(string $alias = null)
    {
        $query = " SELECT * FROM $this->tableName ";
        $this->parameters = [];

        if (!is_null($alias)) {
            $query .= " AS $alias ";
        }
        
        $this->queryText = $query;
        return $this;
    }

    protected function select(string ...$columns)
    {
        $this->queryText = str_ireplace('*', implode(', ', $columns), $this->queryText);
        return $this;
    }

    protected function innerJoin(string $tableName, string $alias, string $predicate)
    {
        $this->addJoin($tableName, $alias, $predicate, 'INNER');
        return $this; 
    }

    protected function leftJoin(string $tableName, string $alias, string $predicate)
    {
        $this->addJoin($tableName, $alias, $predicate, 'LEFT');
        return $this; 
    }

    protected function rightJoin(string $tableName, string $alias, string $predicate)
    {
        $this->addJoin($tableName, $alias, $predicate, 'RIGHT');
        return $this; 
    }

    protected function fullJoin(string $tableName, string $alias, string $predicate)
    {
        $this->addJoin($tableName, $alias, $predicate, 'FULL');
        return $this; 
    }

    protected function addJoin(string $tableName, string $alias, string $predicate, string $type)
    {
        $tableName = $this->addPrefix($tableName);
        $this->queryText .= " $type JOIN $tableName AS $alias ON $predicate ";
    }
    
    protected function where(string $predicate, $parameter = null)
    {
        $this->addWhere($predicate, $parameter);
        return $this; 
    }

    protected function andWhere(string $predicate, $parameter = null)
    {
        $this->addWhere($predicate, $parameter, 'AND');
        return $this; 
    }

    protected function orWhere(string $predicate, $parameter = null)
    {
        $this->addWhere($predicate, $parameter, 'OR');
        return $this; 
    }

    protected function addWhere(string $predicate, $parameter, $type = 'AND')
    {
        if (!is_null($parameter)) {
            $this->setParameter($parameter);
        }

        if (strpos($this->queryText, 'WHERE') === false) {
            $type = 'WHERE';
        }

        $this->queryText .= " $type $predicate ";
    }

    protected function parsePredicates(array $predicates)
    {
        if (empty($predicates)) {
            return $this;
        }

        foreach ($predicates as $columnName => $parameter) {
            if (is_array($parameter)) {
                $predicate = $parameter;
                $columnName = $predicate[0];
                $condition = mb_strtoupper($predicate[1], 'UTF-8');
                $parameter = $predicate[2];
                $parameterType = $this->getParameterType($parameter);
                if ($condition == 'IN') {
                    $parameterType = '('.implode(',', $parameter).')';
                }
                $predicate = "`$columnName` $condition $parameterType";
            } else {
                $parameterType = $this->getParameterType($parameter);
                $predicate = "`$columnName` = $parameterType";
            }
            $this->addWhere($predicate, $parameter, 'AND');
        }

        return $this;
    }

    protected function setParameter($parameter)
    {
        if (is_array($parameter)) {
            return;
        } elseif (is_bool($parameter)) {
            $parameter = $parameter ? 1 : 0;
        }

        $this->parameters[] = $parameter;
    }

    protected function getParameterType($parameter)
    {
        if (is_string($parameter) || is_array($parameter)) {
            return '%s';
        } elseif (is_float($parameter)) {
            return '%f';
        } elseif (is_int($parameter) || is_bool($parameter)) {
            return '%d';
        } else {
            return '%%';
        }
    }

    protected function limit(int $number)
    {
        $this->parameters[] = $number;
        $this->queryText .= " LIMIT %d ";
        return $this; 
    }

    protected function offset(int $number)
    {
        $this->parameters[] = $number;
        $this->queryText .= " OFFSET %d ";
        return $this; 
    }

    protected function groupBy(string ...$columns)
    {
        $this->queryText .= " GROUP BY ".implode(', ', $columns)." ";
        return $this; 
    }

    protected function orderBy(string $column, string $predicate)
    {
        $predicate = strtoupper($predicate);
        $this->queryText .= " ORDER BY $column $predicate ";
        return $this; 
    }

    protected function addPrefix(string $table)
    {
        return $this->$prefix . $table;
    }

    protected function getQuery()
    {
        return $this->queryText;
    }

    protected function getParameters()
    {
        return $this->parameters;
    }

}