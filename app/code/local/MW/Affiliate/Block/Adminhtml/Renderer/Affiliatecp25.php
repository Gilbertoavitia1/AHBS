<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Affiliatecp25 extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $return = $row['cp'] * .25;
    	return $return;
   }
}