<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Saletype extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['referral_sponsor'])) return '';
        
        $order = Mage::getModel('sales/order')->loadByIncrementId($row['increment_id']);
        $items = $order->getAllVisibleItems();
        
        $type;
        foreach($items as $i):
            $_product = Mage::getModel('catalog/product')->load($i->getProductId());
            $type = $type == 3 ? 3 : (($_product->getTypeId() == 'bundle') ? ($type == 2 ? 3 : 1): ($type == 1 ? 3 : 2));
                                                        
        endforeach;
        
        $type = $type == 1 ? 'Paquete' : ($type == 3 ? 'Paquete y Recompra' : 'Recompra');
   
        return $type;
    }

}