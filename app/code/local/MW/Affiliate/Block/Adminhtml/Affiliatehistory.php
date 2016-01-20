<?php
class MW_Affiliate_Block_Adminhtml_Affiliatehistory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_affiliatehistory';
		$this->_blockGroup = 'affiliate';
		$this->_headerText = Mage::helper('affiliate')->__('Commission History');
			
		parent::__construct();
		$this->_removeButton('add');
		$this->_addButton('import', array(
            'label'     => Mage::helper('affiliate')->__('Update Affiliate Transactions via CSV'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/import') .'\')',
            'class'     => 'add',
        ));
		
	}
}