<?php

class Sparx_Storelocator_Model_Mysql4_Storelocator_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storelocator/storelocator');
    }
}