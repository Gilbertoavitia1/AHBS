<?php

class MW_Affiliate_Block_Adminhtml_Affiliatememberpending_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'customer_id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliatememberpending';
        $this->_removeButton('delete');
        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_addButton('approve', array(
            'label'     => Mage::helper('affiliate')->__('Approve'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/approve',array('id'=>$this->getRequest()->getParam('id'))) .'\')',
            'class'     => 'add',
        ));
        /*
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Member'));
        $this->_removeButton('delete');
        //$this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Member'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('affiliate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'affiliate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'affiliate_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";*/
    }

    public function getHeaderText()
    {   
    	$customer_id = $this->getRequest()->getParam('id');
    	if(isset($customer_id)){
    		$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    		return Mage::helper('affiliate')->__($this->htmlEscape($name));
    	}
//        if( Mage::registry('affiliate_data_member') && Mage::registry('affiliate_data_member')->getId() ) {
//            return Mage::helper('affiliate')->__("Edit Member '%s'", $this->htmlEscape(Mage::registry('affiliate_data_member')->getCustomerId()));
//        } else {
//            return Mage::helper('affiliate')->__('Add Member');
//        }
    }
}