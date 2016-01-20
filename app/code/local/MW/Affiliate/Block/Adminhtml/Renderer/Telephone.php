<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Telephone extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['entity_id'])) {
    		return '';
    	}
        
        $customer = Mage::getModel('customer/customer')->load($row['entity_id']);
        
        if ($customer->getPrimaryBillingAddress()){
            return $customer->getPrimaryBillingAddress()->getTelephone();
        }
        else{
            return "";
        }
    }
}