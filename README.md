#FastZendPaginator
sources PhpStorm

Usage:

$adapter = new \Application\Library\UDbSelect($select,$this->getDbAdapter(),new UHydratingResultSet(new UserHydrator(),$this->getEntityPrototype()));

$paginator = new \Application\Library\UPaginator($adapter);

return $paginator;


Fixes cache issues with zend 2.2.4 paginator and makes use of Memcached possible
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
          <b>//fails to set Tags when Memcached or Redis Cache storage is used </b>
           static::$cache->setTags($cacheId, array($this->_getCacheInternalId()));
        }

        return $items;
    }
Zend looks for the setTags() method in the original Zend\Paginator\Paginator and fails in \Zend\Cache\Storage\Adapter\Memcached 
because the method setTags isn't found.

