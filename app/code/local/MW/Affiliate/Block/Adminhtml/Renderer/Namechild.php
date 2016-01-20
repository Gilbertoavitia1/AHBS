<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Namechild extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$customer_id = 0;
        
        if (isset($row['child_id'])){
             $customer_id = $row['child_id'];
        }        
        
        if ($customer_id == 0)
            return '';
        
    	return Mage::getModel('customer/customer')->load($customer_id)->getName();
    }

}