<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Add_Tab_Field_Custom extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attribute=array()) {
		parent::__construct($attribute);
	}
	
	public function getElementHtml() {
		$html = '<div id="' . $this->getHtmlId() . '"' . $this->serialize($this->getHtmlAttributes()) . '></div>';
		$html .= $this->getAfterElementHtml();
		
		return $html;
	}
}