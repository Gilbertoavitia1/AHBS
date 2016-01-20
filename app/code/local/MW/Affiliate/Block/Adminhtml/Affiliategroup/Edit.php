<?php

class MW_Affiliate_Block_Adminhtml_Affiliategroup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'group_id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliategroup';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Group'));
        //$this->_removeButton('delete');
        $this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Group'));
		
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
        ";
    }

    public function getHeaderText()
    {   
//    	$group_id = $this->getRequest()->getParam('id');
//    	if(isset($group_id)){
//    		$group_name = Mage::getModel('affiliate/affiliategroup')->load($group_id)->getGroupName();
//    		return Mage::helper('affiliate')->__($this->htmlEscape($group_name));
//    	}
        if( Mage::registry('affiliate_data_group') && Mage::registry('affiliate_data_group')->getId() ) {
            return Mage::helper('affiliate')->__("Edit Group '%s'", $this->htmlEscape(Mage::registry('affiliate_data_group')->getGroupName()));
        } else {
            return Mage::helper('affiliate')->__('Add Group');
        }
    }
}