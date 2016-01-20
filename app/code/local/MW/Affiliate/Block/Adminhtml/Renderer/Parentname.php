<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Parentname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['customer_invited'])) return '';
    	return Mage::getModel('customer/customer')->load($row['customer_invited'])->getName();
    }

}