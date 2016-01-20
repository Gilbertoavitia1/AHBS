<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Emailreferral extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['customer_id'])) return '';
    	$email = Mage::getModel("customer/customer")->load($row['customer_id'])->getEmail();    
    						  	
    	return $email;
    }

}