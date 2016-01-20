<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Rfslevelspeople extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {        
        $store = $row['store_id'];        
        $lvls = Mage::getStoreConfig('binary/faststart/levelstopay', $store);
        
        $parent;
        for ($i=0; $i<$lvls; $i++) {            
            $customer_id = ($i == 0) ? $row['customer_id'] : $parent['customer_id'];            
            if (($parent['referral_sponsor']) || ($i ==0)){
                $parent = Mage::helper('affiliate_binary_faststart')->_getSponsorParent($customer_id);
                $name = Mage::getModel('customer/customer')->load($parent['customer_id'])->getName();
                $data .= "<strong>{$parent['referral_code']}</strong> | <span style='color: #008000;font-weight: bold;'>{$name}</span>";
            }else{
                $data .= "<span style='color: #ff0000; font-weight: bold;'>N/A</span>";
            }
            
            $data .='<br>';
        }
        
    	return $data;
    }

}