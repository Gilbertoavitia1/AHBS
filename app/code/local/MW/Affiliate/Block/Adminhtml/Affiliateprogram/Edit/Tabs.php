<?php

class MW_Affiliate_Block_Adminhtml_Affiliateprogram_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliateprogram_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('affiliate')->__('Affiliate Program Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_program_detail', array(
          'label'     => Mage::helper('affiliate')->__('Program Detail'),
          'title'     => Mage::helper('affiliate')->__('Program Detail'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_form')->toHtml(),
          'active'    =>true,
      ));
      $this->addTab('form_conditions', array(
          'label'     => Mage::helper('affiliate')->__('Conditions'),
          'title'     => Mage::helper('affiliate')->__('Conditions'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_conditions')->toHtml(),
      	  //'active'    =>true,
      ));
      $this->addTab('form_actions', array(
          'label'     => Mage::helper('affiliate')->__('Affiliate Commission/Customer Discount'),
          'title'     => Mage::helper('affiliate')->__('Affiliate Commission/Customer Discount'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_actions')->toHtml(),
      	  //'active'    =>true,
      ));
     $this->addTab('form_program_group', array(
             'label'     => Mage::helper('affiliate')->__('Add Group'),
             'url'       => $this->getUrl('*/*/group', array('_current' => true)),
             'class'     => 'ajax',
      		 //'active'    =>true,
      ));
      $this->addTab('form_program_transaction', array(
          'label'     => Mage::helper('affiliate')->__('Program Transactions'),
          'title'     => Mage::helper('affiliate')->__('Program Transactions'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_transaction')->toHtml(),
          //'active'    =>true,
     	   ));
     
      return parent::_beforeToHtml();
  }
}