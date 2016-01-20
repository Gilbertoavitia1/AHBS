<?php

class Mage_Firstdatae4_Model_Mysql4_Recurring_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('firstdatae4/recurring');
    }
}