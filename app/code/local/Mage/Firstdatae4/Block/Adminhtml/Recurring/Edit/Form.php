<?php

class Mage_Firstdatae4_Block_Adminhtml_Recurring_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('firstdatae4/recurring.phtml');
        
    }

    protected function _prepareForm()
    {
                
//        $form = new Varien_Data_Form(array(
//            'id'        => 'edit_form',
//            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
//            'method'    => 'post',
//            'enctype'   => 'multipart/form-data'
//        ));
      
//        $form->setUseContainer(true);
//        $this->setForm($form);

//        $data = Mage::registry('recurring_data')->getData();
//        $customer = Mage::getModel("customer/customer")->load($data['customer_id']);
//        
//        $details = Mage::getModel('firstdatae4/detail')->getCollection();
//        $details->addFieldToFilter('recurring_id',$data['recurring_id']);
//        
//        $fieldset = $form->addFieldset('customer_information', array(
//            'legend'      => Mage::helper('firstdatae4')->__('Customer Information'),
//            'class'       => '',
//            )
//        );
//        
//        $fieldset->addField('customer_name', 'label', array(
//            'label'     => Mage::helper('firstdatae4')->__('Customer Name'),
//            'name'      => 'customer_name',
//            'value'     => $customer->getName()
//        ));
//        
//        $fieldset->addField('customer_email', 'label', array(
//            'label'     => Mage::helper('firstdatae4')->__('Email'),
//            'name'      => 'customer_email',
//            'value'     => $customer->getEmail()
//        ));
//        
//        $fieldset->addField('created_date', 'label', array(
//            'label'     => Mage::helper('firstdatae4')->__('Created Date'),
//            'name'      => 'created_date',
//            'value'     => $data['created_date']
//        ));
//        
//        
//        $fieldset = $form->addFieldset('sale_information', array());
//        
//        foreach ($details as $key => $value) {
//            $fieldset->addField('sale_'.$key, 'label', array(
//                'label'     => Mage::helper('firstdatae4')->__('Customer Name'),
//                'name'      => 'customer_name',
//                'value'     => $value['product_id']
//            ));
//        }
//        
//        //We call it extended_label
//        $fieldset->addType('extended_label','Mage_Firstdatae4_Lib_Varien_Data_Form_Element_ExtendedLabel');
//
//        $fieldset->addField('mycustom_element', 'extended_label', array(
//            'name'          => 'mycustom_element',
//            'required'      => false,
//            'value'         => 'dasd',
//            'class'         => ' true',
//        ));
//        return parent::_prepareForm();
        

//      $form->setUseContainer(true);
//      $this->setForm($form);
//      return parent::_prepareForm();
  }
}