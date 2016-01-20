<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['order_id'])) return '';
    	$order = Mage::getModel("sales/order")->loadByIncrementId($row['order_id']);
    	$url = "adminhtml/sales_order/view";
		$result='';
    	$result = Mage::helper('affiliate')->__("<b><a href=%s>%s</a></b>",Mage::helper('adminhtml')->getUrl($url,array('order_id'=>$order->getId())),$row['order_id']); 
    
    	return $result;
    }

}