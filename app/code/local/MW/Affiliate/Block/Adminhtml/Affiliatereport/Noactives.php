<?php
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Noactives extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
	    $this->_controller = 'adminhtml_affiliatereport_noactives';
	    $this->_headerText = Mage::helper('affiliate')->__('Affiliate No Actives');
	    $this->_blockGroup = 'affiliate';
	    parent::__construct();
	    $this->_removeButton('add');
  }
  
}