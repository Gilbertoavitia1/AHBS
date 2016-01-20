<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Preferredprofit extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
   public function render(Varien_Object $row) {
        $col = Mage::getModel('affiliate/affiliatebinarypreferred')
            ->getCollection()
            ->addFieldToFilter('customer_id', $row['customer_id'])
            ->addFieldToFilter('start_date', $row['start_date'])
            ->getData();
        
        $mxn = 0;
        $usd = 0;
        foreach ($col as $key => $value) {
            if ($value['currency'] == "MXN")
                $mxn = $mxn + $value['profit'];
            else
                $usd = $usd + $value['profit'];
        }
        
        return "<strong>MXN</strong> ". number_format($mxn, 2). ' | '. "<strong>USD</strong> ".number_format($usd, 2);
    }
}