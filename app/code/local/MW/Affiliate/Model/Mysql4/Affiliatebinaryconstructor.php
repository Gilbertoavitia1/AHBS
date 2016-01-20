<?php

class MW_Affiliate_Model_Mysql4_Affiliatebinaryconstructor extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('affiliate/affiliatebinaryconstructor', 'id');
    }
}