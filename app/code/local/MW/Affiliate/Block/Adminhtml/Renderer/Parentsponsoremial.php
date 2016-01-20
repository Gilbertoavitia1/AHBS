<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Parentsponsoremial extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['referral_sponsor'])) return '';
        
        
        $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
  			->addFieldToFilter('referral_code',$row['referral_sponsor']);
        
        foreach ($collection as $collectionCustomer) {
                return Mage::getModel('customer/customer')->load($collectionCustomer->getCustomerId())->getEmail();
        }
    }

}