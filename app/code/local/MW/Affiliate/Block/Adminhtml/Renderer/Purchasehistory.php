<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Purchasehistory extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
          // load tags
        $result = '';
        $collection = Mage::getModel('sales/order')->loadByIncrementId($row['order_id']);
      	$grand_total = $collection->getGrandTotal();
      	return Mage::helper('core')->currency($grand_total,true,false);
		
  		/*if($grand_total != ''){
			$grand_total = number_format($grand_total, 2);
    		$result .= Mage::helper('checkout')->formatPrice($grand_total) . ' ' .  Mage::app()->getBaseCurrencyCode();
  		}
      	
      	$return = Mage::helper('affiliate')->__($result);
    	return $return;*/
   }
}