<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Commissionholdingendtime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['transaction_time'])) {
    		return '';
    	}
    	$holdingDays  = intval(Mage::getStoreConfig('affiliate/general/commission_holding_period'));
    	
    	return date('M d, Y', strtotime($row['transaction_time']) + $holdingDays*86400);
    	
   	}

}