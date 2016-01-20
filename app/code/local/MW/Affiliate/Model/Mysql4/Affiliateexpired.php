<?php

class MW_Affiliate_Model_Mysql4_Affiliateexpired extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('affiliate/affiliateexpired', 'customer_id');
    }
}