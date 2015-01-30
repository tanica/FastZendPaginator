<?php

namespace Application\Library;



use Zend\Paginator\Paginator;

class UPaginator extends Paginator {

    /**
     * Get the internal cache id
     * Depends on the adapter and the item count per page
     *
     * Used to tag that unique Paginator instance in cache
     *
     * @return string
     */
    protected function _getCacheInternalId()
    {
        return md5(serialize(array(
            $this->getAdapter()->getHash(),
            $this->getItemCountPerPage()
        )));
    }
    
    public function setTags(){
         
    }
    
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

       // if (!$items instanceof Traversable) {
           // $items = new \ArrayIterator($items);
       // }

        if ($this->cacheEnabled()) {
            $cacheId = $this->_getCacheId($pageNumber);
            static::$cache->setItem($cacheId, $items);
            //disable this so memcached won't fail
            //static::$cache->setTags($cacheId, array($this->_getCacheInternalId()));
        }

        return $items;
    }
}