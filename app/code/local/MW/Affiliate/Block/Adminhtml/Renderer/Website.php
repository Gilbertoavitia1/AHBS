<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['customer_id'])) {
    		return '';
    	}
    	
    	$sites = Mage::getModel('affiliate/affiliatewebsitemember')
    			 ->getCollection()
    			 ->addFieldToFilter('customer_id', array('eq' => $row['customer_id']));
    	
    	$siteList = array();
    	foreach($sites as $site) {
    		$siteList[] = $site->getDomainName();
    	}
    	
		return implode(', ', $siteList);    
    }

}