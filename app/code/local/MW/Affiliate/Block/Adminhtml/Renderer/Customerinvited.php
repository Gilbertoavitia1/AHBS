<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Customerinvited extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['customer_invited'])) return '';
    	if($row['customer_invited']== 0 ) return '';
        
        $collectionCustomer = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id',$row['customer_invited'])
            ->getFirstItem();;
         
    	$name = Mage::getModel("customer/customer")->load($row['customer_invited'])->getName();    
    	$email = Mage::getModel("customer/customer")->load($row['customer_invited'])->getEmail();   
        return $collectionCustomer->getReferralCode().'<br>'.$email.'<br>'.$name;
        
    }

}