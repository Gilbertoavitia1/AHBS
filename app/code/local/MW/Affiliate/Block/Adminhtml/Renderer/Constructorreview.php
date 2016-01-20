<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Constructorreview extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {        
        $order = json_decode($row['order']);
        
        foreach ($order as $key => $value) {
            $url = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id' => $value->id));
            $total = number_format(($value->currency == 'USD' ? $value->points : $value->points * 10),2);
            $result .= "<a href='{$url}' target='_blank'>{$value->order}</a> | $ {$total} {$value->currency}<br>";
        }
        
    	return $result;
   }
}