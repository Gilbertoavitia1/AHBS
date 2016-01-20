<?php

class MW_Affiliate_Model_Typeinvitation extends Varien_Object
{
	const NON_REFERRAL				= 0;
	const REFERRAL_LINK				= 1;		//haven't change points yet
    const REFERRAL_CODE				= 2;
  
    static public function getOptionArray()
    {
        return array(
        	self::NON_REFERRAL    		=> Mage::helper('affiliate')->__('Non Referral'),
            self::REFERRAL_LINK    		=> Mage::helper('affiliate')->__('Referral Link'),
            self::REFERRAL_CODE  		=> Mage::helper('affiliate')->__('Referral Code'),
        );
    }
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}