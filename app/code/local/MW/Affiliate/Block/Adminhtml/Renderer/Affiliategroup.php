<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Affiliategroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
          // load tags
        $result = '';
        $collection = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
        				->addFieldToFilter('customer_id',$row['customer_id']);
        $group_id = '';
        foreach ($collection as $c) {
        	$group_id = $c->getGroupId();
        	break;
        }
      	//$grand_total = $collection->getGroupId();
		
  		if($group_id != ''){
  			$collection_group = Mage::getModel('affiliate/affiliategroup')->load($group_id);
    		$result .= $collection_group->getGroupName();
  		}
      	
      	$return = Mage::helper('affiliate')->__($result);
    	return $return;
   }
}