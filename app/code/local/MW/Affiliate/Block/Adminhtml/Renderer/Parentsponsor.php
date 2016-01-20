<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Parentsponsor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['referral_sponsor'])) return '';
        
        
        $collectionCustomer = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('referral_code',$row['referral_sponsor'])
            ->getFirstItem();
        
        $name = Mage::getModel('customer/customer')->load($collectionCustomer->getCustomerId())->getName();
        $email = Mage::getModel('customer/customer')->load($collectionCustomer->getCustomerId())->getEmail();
        return $collectionCustomer->getReferralCode()."<br>".$email.'<br>'.$name;
            
    }

}