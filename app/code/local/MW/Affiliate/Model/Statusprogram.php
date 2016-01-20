<?php

class MW_Affiliate_Model_Statusprogram extends Varien_Object
{
	const ENABLED				= 1;		//haven't change points yet
    const DISABLED				= 2;
	

    static public function getOptionArray()
    {
        return array(
            self::ENABLED    				=> Mage::helper('affiliate')->__('Enabled'),
            self::DISABLED  			 	=> Mage::helper('affiliate')->__('Disabled'),
        );
    }
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}