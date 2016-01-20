<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Emailreport extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['customer_invited'])) return '';
    	$email = Mage::getModel("customer/customer")->load($row['customer_invited'])->getEmail();    
    						  	
    	return $email;
    }

}