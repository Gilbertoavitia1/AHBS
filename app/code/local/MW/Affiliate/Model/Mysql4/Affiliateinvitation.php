<?php

class MW_Affiliate_Model_Mysql4_Affiliateinvitation extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the affiliate_id refers to the key field in your database table.
        $this->_init('affiliate/affiliateinvitation', 'invitation_id');
    }
}