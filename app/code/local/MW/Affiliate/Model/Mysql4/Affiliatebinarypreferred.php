<?php

class MW_Affiliate_Model_Mysql4_Affiliatebinarypreferred extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('affiliate/affiliatebinarypreferred', 'id');
    }
}