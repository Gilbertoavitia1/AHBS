<?php
class MW_Affiliate_Block_Adminhtml_Affiliatecredithistory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_affiliatecredithistory';
		$this->_blockGroup = 'affiliate';
		$this->_headerText = Mage::helper('affiliate')->__('Transaction History');
			
		parent::__construct();
		$this->_removeButton('add');
		
	}
}