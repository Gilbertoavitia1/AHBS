<?php

class Mage_Firstdatae4_Block_Adminhtml_Recurring_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'firstdatae4';
        $this->_controller = 'adminhtml_recurring';
        $this->_removeButton('save');
//        $this->_removeButton('delete');
    }

    public function getHeaderText()
    {
        return Mage::helper('firstdatae4')->__("Recurring Details");
    }
}