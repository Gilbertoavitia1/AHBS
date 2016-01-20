<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinaryresidual_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $id = $this->getRequest()->getParam('id');
        $data = Mage::getModel('affiliate/affiliatebinaryresidual')->load($id)->getData();
        
//        echo '<pre>';print_R($data);echo '</pre>';
        $fieldset = $form->addFieldset('preferred_form',
            array('legend' => Mage::helper('affiliate')->__('Description')));
        
        
        $fieldset->addField('name', 'label', array(
            'label'     => Mage::helper('affiliate')->__('Name'),
            'name'      => 'name',
            'value'     => Mage::getModel('customer/customer')->load($data['customer_id'])->getName()
        ));
        
        $fieldset->addField('email', 'label', array(
            'label'     => Mage::helper('affiliate')->__('Email'),
            'name'      => 'email',
            'value'     => Mage::getModel('customer/customer')->load($data['customer_id'])->getEmail()
        ));
        
        $fieldset->addField('period', 'label', array(
            'label'     => Mage::helper('affiliate')->__('Period'),
            'name'      => 'period',
            'value'     => $data['start_date']. ' - '.$data['end_date']
        ));
        
        $fieldset->addField('vp', 'label', array(
            'label'     => Mage::helper('affiliate')->__('VP'),
            'name'      => 'vp',
            'value'     => $data['vp']
        ));
        
        $fieldset->addField('vg', 'label', array(
            'label'     => Mage::helper('affiliate')->__('VG'),
            'name'      => 'vg',
            'value'     => $data['vg']
        ));
        
        $fieldset->addField('vpleft', 'label', array(
            'label'     => Mage::helper('affiliate')->__('Pierna Izquierda'),
            'name'      => 'vpleft',
            'value'     => $data['vpleft']
        ));
        
        $fieldset->addField('vpright', 'label', array(
            'label'     => Mage::helper('affiliate')->__('Pierna Derecha'),
            'name'      => 'vpright',
            'value'     => $data['vpright']
        ));
        
        $fieldset->addField('paid', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Paid'),
            'name'      => 'paid',
            'value'     => $data['paid']
        ));
        
        
        $fieldset->addField('hold', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Hold'),
            'name'      => 'hold',
            'value'     => $data['hold']
        ));
        
        $fieldset->addField('hold_side', 'select', array(
            'label'     => Mage::helper('affiliate')->__('Hold Side'),
            'name'      => 'hold_side',
            'values'    => array(1=>'Izquierda', 2=>'Derecha'),
            'value'     => $data['hold_side']
        ));
        
        return parent::_prepareForm();
    }

}