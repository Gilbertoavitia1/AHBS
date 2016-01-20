<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Showcustomerinvited extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['show_customer_invited'])) return '';
    	if($row['show_customer_invited'] == 0 ) return '';
    	$email = Mage::getModel("customer/customer")->load($row['show_customer_invited'])->getEmail();    
    						  	
    	return $email;
    }

}