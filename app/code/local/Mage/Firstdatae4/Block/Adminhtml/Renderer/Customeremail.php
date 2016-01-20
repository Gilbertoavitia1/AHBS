<?php

class Mage_Firstdatae4_Block_Adminhtml_Renderer_Customeremail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['customer_id'])) return '';
    	if($row['customer_id']== 0 ) return '';
            $name = Mage::getModel("customer/customer")->load($row['customer_id'])->getEmail();    
    						  	
    	return $name;
    }

}