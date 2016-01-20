<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Rfslevels extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $store = $row['store_id'];        
        $lvls = Mage::getStoreConfig('binary/faststart/levelstopay', $store);
    	return $lvls;
    }

}