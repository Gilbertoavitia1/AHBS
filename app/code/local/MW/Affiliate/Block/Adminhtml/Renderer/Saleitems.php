<?php

class MW_Affiliate_Block_Adminhtml_Renderer_SaleItems extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['referral_sponsor'])) return '';
        
        $order = Mage::getModel('sales/order')->loadByIncrementId($row['increment_id']);
        $items = $order->getAllVisibleItems();
        
        $desc;
        foreach($items as $i):
            $_product = Mage::getModel('catalog/product')->load($i->getProductId());
        $desc .= number_format($i->getQtyOrdered(), 0, '', '')." <strong>x</strong> ".$_product->getName().'<br>';
               
        endforeach;
   
        return $desc;
    }

}