<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Emailaffiliatemember extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['email'])) return '';
    	$cutomer = Mage::getModel("customer/customer")->getCollection()
    						  ->addFieldToFilter('email',$row['email'])
    						  ->getFirstItem();    
    						  	
    	$url = "affiliate/adminhtml_affiliatemember/edit";
		$result='';
    	$result = Mage::helper('affiliate')->__("<b><a href=%s>%s</a></b>",Mage::helper('adminhtml')->getUrl($url,array('id'=>$cutomer->getId())),$row['email']); 
    	return $result;
    }

}