#FastZendPaginator

Usage:

$adapter = new \Application\Library\UDbSelect($select,$this->getDbAdapter(),new UHydratingResultSet(new UserHydrator(),$this->getEntityPrototype()));
$paginator = new \Application\Library\UPaginator($adapter);

return $paginator;


//fixes cache issues with zend 2.2.3 paginator and makes use of Memcached possible
//Zend looks for the setTags() method in the original Zend\Paginator\Paginator and fails in \Zend\Cache\Storage\Adapter\Memcached 
because the method setTags isn't found.

