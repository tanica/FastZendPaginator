<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28-01-2015
 * Time: 05:55 PM
 */
namespace Application\Library;

class UHydratingResultSet extends  \Zend\Db\ResultSet\HydratingResultSet {

    protected $temp;


    public function __sleep()
    {

        $this->buffer();
        $this->temp = $this->toArray();

        return array('hydrator','objectPrototype','buffer','count','fieldCount','position','temp');

    }

    public function __wakeup(){

        $this->initialize($this->temp);

    }

}