<?php

class MW_Affiliate_Block_Adminhtml_Affiliateprogram_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form_program_detail = new Varien_Data_Form();
      $this->setForm($form_program_detail);
      $fieldset = $form_program_detail->addFieldset('affiliate_form', array('legend'=>Mage::helper('affiliate')->__('Program Information')));
      
      $program_id = $this->getRequest()->getParam('id'); 
      $affiliate_program = Mage::getModel('affiliate/affiliateprogram')->load($program_id);
      $total_commission = $affiliate_program -> getTotalCommission();
      $status = $affiliate_program -> getStatus();
      $total_commission1 =Mage::helper('core')->currency($total_commission,true,false);
      
      $fieldset->addField('send_mail', 'checkbox', array(
          'label'     => Mage::helper('affiliate')->__('Notify Affiliate of program changes via email'),
          'onclick'   => 'this.value = this.checked ? 1 : 0;',
          'name'      => 'send_mail',
      ));
      $fieldset->addField('program_name', 'text', array(
          'label'     => Mage::helper('affiliate')->__('Program Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'program_name',
      ));
	  $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('affiliate')->__('General description of Affiliate Program'),
	 		'class'     => 'required-entry',
	  		'required'  => true,
            'title' => Mage::helper('affiliate')->__('General Description Program'),
            //'style' => 'width: 100%; height: 150px;',
        ));
    /*  $fieldset->addField('commission', 'text', array(
          'label'     => Mage::helper('affiliate')->__('Commission'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'commission',
      	  'note' => Mage::helper('affiliate')->__('Format x or y% (x - fixed commission amount/ y% - percent of product value)'),
      ));
       $fieldset->addField('discount', 'text', array(
          'label'     => Mage::helper('affiliate')->__('Discount'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'discount',
       	  'note' => Mage::helper('affiliate')->__('Format x or y% (x - fixed discount amount/ y% - percent of product value)'),
      ));*/
  	  //Store View
  	  if (!Mage::app()->isSingleStoreMode()) {
              $fieldset->addField('store_view', 'multiselect', array(
                    'name'      => 'store_view[]',
                    'label'     => Mage::helper('affiliate')->__('Store View'),
                    'title'     => Mage::helper('affiliate')->__('Store View'),
                    'required'  => true,
                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                    //'disabled'  => $isElementDisabled                
              ));
       } 
       else {
            $fieldset->addField('store_view', 'hidden', array(
                'name'      => 'store_view[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
           // $model->setStoreId(Mage::app()->getStore(true)->getId());
        }
      $fieldset->addField('program_position', 'text', array(
          'label'     => Mage::helper('affiliate')->__('Program priority'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'program_position',
      	  'note' => Mage::helper('affiliate')->__('Important: Only 1 Affiliate program will take effect if purchased product belongs to 2 or more programs. Set program executing priority under Configuration-Manage Affiliate Commission and Customer Discount'),
      ));
      $fieldset->addField('status_program', 'select', array(
          'label'     => Mage::helper('affiliate')->__('Status'),
          'name'      => 'status_program',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('affiliate')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('affiliate')->__('Disabled'),
              ),
          ),
          'note' => Mage::helper('affiliate')->__('Enable and save this Affiliate program to activate.'),
      ));
       $fieldset->addField('start_date', 'date', array(
          'label'     => Mage::helper('affiliate')->__('Start Date'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'start_date',
	  	  'image'  => $this->getSkinUrl('images/grid-cal.gif'),
		  //'format' => 'yyyy-MM-dd',
		  'format' => 'yyyy-MM-dd',
		  //'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
       	  //'time'   =>true,
      ));
      $fieldset->addField('end_date', 'date', array(
          'label'     => Mage::helper('affiliate')->__('End Date'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'end_date',
      	  'image'  => $this->getSkinUrl('images/grid-cal.gif'),
      	  //'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
      	  //'time'   =>true,
		  //'format' => 'yyyy-MM-dd',
		  'format' => 'yyyy-MM-dd',  
      ));
      $fieldset->addField('total_members', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Total Members'),
          'name'      => 'total_members',
      	  'readonly' => 'readonly',
      
      ));
      $fieldset->addField('total_commission1', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Total Commission'),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getAffiliateDataProgram() )
      {
          $form_program_detail->setValues(Mage::getSingleton('adminhtml/session')->getAffiliateDataProgram());
          Mage::getSingleton('adminhtml/session')->setAffiliateData(null);
      } elseif ( Mage::registry('affiliate_data_program') ) {
      	  //Zend_Debug::dump(Mage::registry('affiliate_data_program')->getData());die();
        $form_program_detail->setValues(Mage::registry('affiliate_data_program')->getData());
        $form_program_detail->getElement('total_commission1')->setValue($total_commission1);
        $form_program_detail->getElement('status_program')->setValue($status);
      }
      return parent::_prepareForm();
  }
}