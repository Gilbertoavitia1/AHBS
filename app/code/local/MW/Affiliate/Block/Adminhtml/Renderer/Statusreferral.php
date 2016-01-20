<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Statusreferral extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['status'])) return '';
    	$transactionDetail = MW_Affiliate_Model_Statusinvitation::getLabel($row['status']); 
    	return $transactionDetail;
    }

}