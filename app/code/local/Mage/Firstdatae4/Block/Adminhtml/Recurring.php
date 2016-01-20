<?php
class Mage_Firstdatae4_Block_Adminhtml_Recurring extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
  public function __construct()
  {
    $this->_controller = 'adminhtml_recurring';
    $this->_blockGroup = 'firstdatae4';
    $this->_headerText = Mage::helper('firstdatae4')->__('Recurring Profiles');
    parent::__construct();
    $this->_removeButton('add');
  }
}
