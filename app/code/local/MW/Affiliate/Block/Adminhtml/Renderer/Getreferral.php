<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Getreferral extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['customer_id'])) {
    		return '';
    	}
        
        $customer = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id',$row['customer_id'])
            ->getFirstItem()
                ->getData();
            
        
        if (sizeof($customer) > 0){
            return "#{$customer['referral_code']}";
        }else{
            return "";
        }
    }

}