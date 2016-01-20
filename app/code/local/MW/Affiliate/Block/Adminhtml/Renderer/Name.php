<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$customer_id = 0;
        if (isset($row['customer_id'])){
             $customer_id = $row['customer_id'];
        }
        
        if (isset($row['parent_id'])){
             $customer_id = $row['parent_id'];
        }
        
        
        if ($customer_id == 0)
            return '';
        
    	return Mage::getModel('customer/customer')->load($customer_id)->getName();
    }

}