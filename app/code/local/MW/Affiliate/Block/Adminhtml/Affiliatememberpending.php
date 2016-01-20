<?php
class MW_Affiliate_Block_Adminhtml_Affiliatememberpending extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_affiliatememberpending';
		$this->_blockGroup = 'affiliate';
		$this->_headerText = Mage::helper('affiliate')->__('Pending Affiliates') . '<p style="width:700px;font-size:12px;color:#000">'.Mage::helper('affiliate')->__('*Pending Affiliates will be assigned to the default group unless otherwise specified in Configuration - General Settings.<br />To reassign Affiliate go to Manage Affiliate Groups').'<p>';
		parent::__construct();
		$this->_removeButton('add');
		
	}
}