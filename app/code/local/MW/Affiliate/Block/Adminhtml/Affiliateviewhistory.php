<?php
class MW_Affiliate_Block_Adminhtml_Affiliateviewhistory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_affiliateviewhistory';
		$this->_blockGroup = 'affiliate';
		//$this->_headerText = Mage::helper('affiliate')->__('Transaction History');
		$this->_headerText = Mage::helper('affiliate')->__('View commission history').' : #'.$this->getRequest()->getParam('orderid');
		parent::__construct();
		$this ->_addbackbutton();
		$this->_removeButton('add');
	}
	public function getBackUrl()
    {
        return $this->getUrl('*/adminhtml_affiliatehistory/');
    }
}