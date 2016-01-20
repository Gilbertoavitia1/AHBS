<?php
class MW_Affiliate_Block_Adminhtml_Affiliatewithdrawn extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_affiliatewithdrawn';
		$this->_blockGroup = 'affiliate';
		$this->_headerText = Mage::helper('affiliate')->__('All Withdrawals History');
			
		parent::__construct();
		$this->_removeButton('add');
		
	}
}