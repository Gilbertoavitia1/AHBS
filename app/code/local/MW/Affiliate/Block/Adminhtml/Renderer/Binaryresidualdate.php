<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Binaryresidualdate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $newformat = date('Y/M/dd',$row['start_date']);
        return $row['start_date'];
        
    }

}