<?php
class MW_Affiliate_Block_Adminhtml_Affiliateinvoiced extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_affiliateinvoiced';
		$this->_blockGroup = 'affiliate';
		$this->_headerText = Mage::helper('affiliate')->__('Affiliate Invoiced Orders');
			
		parent::__construct();
		$this->_removeButton('add');
		$this->_addButton('import', array(
            'label'     => Mage::helper('affiliate')->__('Import Affiliate Invoiced Orders'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/import') .'\')',
            'class'     => 'add',
        ));
		
	}
}