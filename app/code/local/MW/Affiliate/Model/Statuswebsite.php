<?php
class MW_Affiliate_Model_Statuswebsite extends Varien_Object
{
	const UNVERIFIED			= 0;		
    const VERIFIED			    = 1;
  
    static public function getOptionArray()
    {
        return array(
            self::UNVERIFIED   				=> Mage::helper('affiliate')->__('Not Verified'),
            self::VERIFIED 			 		=> Mage::helper('affiliate')->__('Verified'),
        );
    }
    
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}