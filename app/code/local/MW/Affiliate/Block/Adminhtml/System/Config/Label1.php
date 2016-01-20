<?php

class MW_Affiliate_Block_Adminhtml_System_Config_Label1 extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {    	    
		$html =  $this->getLayout()->createBlock('affiliate/adminhtml_system_config_label1info')->setTemplate('mw_affiliate/system/config/label1.phtml')->toHtml();
        return $html;
    }
}