<?php

namespace Application\Library;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;

class UDbSelect extends  DbSelect {

    public function getHash() {

        $select = clone $this->select;
        $key = $this->sql->getSqlStringForSqlObject($select);
        return md5($key);

    }
    
    public function getItems($offset, $itemCountPerPage)
    {
        $select = clone $this->select;
        $select->offset($offset);
        $select->limit($itemCountPerPage);
        
        $statement = $this->sql->prepareStatementForSqlObject($select);
        
        $result    = $statement->execute();
        $resultSet = clone $this->resultSetPrototype;
        $resultSet->initialize($result);

        return $resultSet;
    }
    
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
}

