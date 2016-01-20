<?php

class MW_Affiliate_Block_Adminhtml_Affiliatehistory_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('affiliatehistory_form', array('legend'=>Mage::helper('affiliate')->__('Update Affiliate Transactions via CSV')));

      
       $fieldset->addField('status_update', 'select', array(
          'label'     => Mage::helper('affiliate')->__('Update Status'),
          'required'  => true,
          'name'      => 'status_update',
       	  'values'    => MW_Affiliate_Model_Status::getOptionAction(),
	  ));
      
      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('affiliate')->__('Upload CSV File'),
          'required'  => true,
          'name'      => 'filename',
	  ));

      return parent::_prepareForm();
  } 
}