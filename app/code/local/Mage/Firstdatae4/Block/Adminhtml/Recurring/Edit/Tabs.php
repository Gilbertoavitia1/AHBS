<?php

class Mage_Firstdatae4_Block_Adminhtml_Recurring_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('recurring_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('firstdatae4')->__('Store Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('firstdatae4')->__('Store Information'),
          'title'     => Mage::helper('firstdatae4')->__('Store Information'),
          'content'   => $this->getLayout()->createBlock('firstdatae4/adminhtml_recurring_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}