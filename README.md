#FastZendPaginator

Fixes cache issues with zend 2.2.4 paginator and makes use of Memcached possible and rewrites count query in UDBSelect for performance benefits

Usage:

//in your Mapper for example
$adapter = new \Application\Library\UDbSelect($select,$this->getDbAdapter(),new UHydratingResultSet(new UserHydrator(),$this->getEntityPrototype()));

$paginator = new \Application\Library\UPaginator($adapter);

return $paginator;


#Src
Zend\Paginator\Adapter\DbSelect 
public function getItemsByPage($pageNumber)
    {
      $pageNumber = $this->normalizePageNumber($pageNumber);

        if ($this->cacheEnabled()) {
            $data = static::$cache->getItem($this->_getCacheId($pageNumber));
            if ($data) {
                return $data;
            }
        }

        $offset = ($pageNumber - 1) * $this->getItemCountPerPage();

        $items = $this->adapter->getItems($offset, $this->getItemCountPerPage());

        $filter = $this->getFilter();

        if ($filter !== null) {
            $items = $filter->filter($items);
        }

        if (!$items instanceof Traversable) {
            $items = new ArrayIterator($items);
        }

        if ($this->cacheEnabled()) {
            $cacheId = $this->_getCacheId($pageNumber);
            static::$cache->setItem($cacheId, $items);
           //fails to setTags() when Zend\Cache\Storage\Adapter\Memcached (or Redis) is used along with Zend Paginator
           static::$cache->setTags($cacheId, array($this->_getCacheInternalId()));
        }

        return $items;
    }


in our Application\Library\UDbSelect rewrote the original count query, it's faster this way

public function count()
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }

        $select = clone $this->select;  
        $select->reset(Select::LIMIT);
        $select->reset(Select::OFFSET);
        $select->reset(Select::ORDER);
        
        $countSelect = new Select;
        $state = $select->getRawState();
        $countSelect->columns(array('c' => new Expression('COUNT(1)')))
                ->from(array(key($state['table']) => current($state['table'])));
        if(!empty($state['joins'])){
            foreach($state['joins'] as $v){
                $countSelect->join($v['name'],$v['on']);
            }
        }
        $countSelect->where($select->getRawState("where"));
        if(!empty($state['group'])){
            $countSelect->group($state['group']);
        }
        $statement = $this->sql->prepareStatementForSqlObject($countSelect);
        $result    = $statement->execute();
        $row       = $result->current();
        
        $this->rowCount = $row['c'];

       return $this->rowCount;
        
    } 


