<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Productname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['product_id'])) return '';
    	return Mage::getModel('catalog/product')->load($row['product_id'])->getName();
    }

}