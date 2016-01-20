<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'add_customer_id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliatemember';
        $this->_mode = 'add';
        
        //$this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Member'));
        $this->_removeButton('save');
        $this->_addButton('save', array(
            'label'     => Mage::helper('adminhtml')->__('Save Member'),
            'onclick'   => 'addForm.submit();',
            'class'     => 'save',
        ), -100);
        $this->_removeButton('delete');
        //$this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Member'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $ajaxUrl = Mage::getUrl('affiliate/adminhtml_affiliatemember/ajaxemail');
        
        $this->_formScripts[] = "
        
        	addForm = new varienForm('add_form', '');
            function toggleEditor() {
                if (tinyMCE.getInstanceById('affiliate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'affiliate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'affiliate_content');
                }
            }

            function saveAndContinueEdit(){
                addForm.submit($('add_form').action+'back/edit/');
            }
        		
            document.observe('dom:loaded', function () {
            	(function() {
	        		new Ajax.Autocompleter('customer_email', 'autocomplete_choices', '$ajaxUrl', {
	        			'paramName': 'customer_email'
    				});
	        	})();
        		
            	if($('auto_withdrawn').value=='1')
				{   
					$('withdrawn_level').up(1).show();
					
				}
				else if($('auto_withdrawn').value=='2')
				{   
					$('withdrawn_level').up(1).hide();
					
				};
				$('auto_withdrawn').observe('change', function () {
					if($('auto_withdrawn').value=='1')
					{   
						$('withdrawn_level').up(1).show();
						
					}
					else if($('auto_withdrawn').value=='2')
					{   
						$('withdrawn_level').up(1).hide();
						
					};
				});
				if($('payment_gateway').value=='banktransfer')
				{   
					$('bank_name').up(1).show();
					if(!$('bank_name').hasClassName('required-entry')) $('bank_name').addClassName('required-entry');
					$('name_account').up(1).show();
					if(!$('name_account').hasClassName('required-entry')) $('name_account').addClassName('required-entry');
					$('bank_country').up(1).show();
					if(!$('bank_country').hasClassName('required-entry')) $('bank_country').addClassName('required-entry');
					$('swift_bic').up(1).show();
					if(!$('swift_bic').hasClassName('required-entry')) $('swift_bic').addClassName('required-entry');
					$('account_number').up(1).show();
					if(!$('account_number').hasClassName('required-entry')) $('account_number').addClassName('required-entry');
					$('re_account_number').up(1).show();
					if(!$('re_account_number').hasClassName('required-entry')) $('re_account_number').addClassName('required-entry');
					$('payment_email').up(1).hide();
					if($('payment_email').hasClassName('required-entry')) $('payment_email').removeClassName('required-entry');
				}
				else if($('payment_gateway').value=='check')
				{   
					$('bank_name').up(1).hide();
					if($('bank_name').hasClassName('required-entry')) $('bank_name').removeClassName('required-entry');
					$('name_account').up(1).hide();
					if($('name_account').hasClassName('required-entry')) $('name_account').removeClassName('required-entry');
					$('bank_country').up(1).hide();
					if($('bank_country').hasClassName('required-entry')) $('bank_country').removeClassName('required-entry');
					$('swift_bic').up(1).hide();
					if($('swift_bic').hasClassName('required-entry')) $('swift_bic').removeClassName('required-entry');
					$('account_number').up(1).hide();
					if($('account_number').hasClassName('required-entry')) $('account_number').removeClassName('required-entry');
					$('re_account_number').up(1).hide();
					if($('re_account_number').hasClassName('required-entry')) $('re_account_number').removeClassName('required-entry');
					$('payment_email').up(1).hide();
					if($('payment_email').hasClassName('required-entry')) $('payment_email').removeClassName('required-entry');
			
				}
				else
				{   
					$('bank_name').up(1).hide();
					if($('bank_name').hasClassName('required-entry')) $('bank_name').removeClassName('required-entry');
					$('name_account').up(1).hide();
					if($('name_account').hasClassName('required-entry')) $('name_account').removeClassName('required-entry');
					$('bank_country').up(1).hide();
					if($('bank_country').hasClassName('required-entry')) $('bank_country').removeClassName('required-entry');
					$('swift_bic').up(1).hide();
					if($('swift_bic').hasClassName('required-entry')) $('swift_bic').removeClassName('required-entry');
					$('account_number').up(1).hide();
					if($('account_number').hasClassName('required-entry')) $('account_number').removeClassName('required-entry');
					$('re_account_number').up(1).hide();
					if($('re_account_number').hasClassName('required-entry')) $('re_account_number').removeClassName('required-entry');
					$('payment_email').up(1).show();
					if(!$('payment_email').hasClassName('required-entry')) $('payment_email').addClassName('required-entry');
				};
        		
				$('payment_gateway').observe('change', function () {
					if($('payment_gateway').value=='banktransfer')
					{   
						$('bank_name').up(1).show();
						if(!$('bank_name').hasClassName('required-entry')) $('bank_name').addClassName('required-entry');
						$('name_account').up(1).show();
						if(!$('name_account').hasClassName('required-entry')) $('name_account').addClassName('required-entry');
						$('bank_country').up(1).show();
						if(!$('bank_country').hasClassName('required-entry')) $('bank_country').addClassName('required-entry');
						$('swift_bic').up(1).show();
						if(!$('swift_bic').hasClassName('required-entry')) $('swift_bic').addClassName('required-entry');
						$('account_number').up(1).show();
						if(!$('account_number').hasClassName('required-entry')) $('account_number').addClassName('required-entry');
						$('re_account_number').up(1).show();
						if(!$('re_account_number').hasClassName('required-entry')) $('re_account_number').addClassName('required-entry');
						$('payment_email').up(1).hide();
						if($('payment_email').hasClassName('required-entry')) $('payment_email').removeClassName('required-entry');
					}
					else if($('payment_gateway').value=='check')
					{   
						$('bank_name').up(1).hide();
						if($('bank_name').hasClassName('required-entry')) $('bank_name').removeClassName('required-entry');
						$('name_account').up(1).hide();
						if($('name_account').hasClassName('required-entry')) $('name_account').removeClassName('required-entry');
						$('bank_country').up(1).hide();
						if($('bank_country').hasClassName('required-entry')) $('bank_country').removeClassName('required-entry');
						$('swift_bic').up(1).hide();
						if($('swift_bic').hasClassName('required-entry')) $('swift_bic').removeClassName('required-entry');
						$('account_number').up(1).hide();
						if($('account_number').hasClassName('required-entry')) $('account_number').removeClassName('required-entry');
						$('re_account_number').up(1).hide();
						if($('re_account_number').hasClassName('required-entry')) $('re_account_number').removeClassName('required-entry');
						$('payment_email').up(1).hide();
						if($('payment_email').hasClassName('required-entry')) $('payment_email').removeClassName('required-entry');
				
					}
					else
					{   
						$('bank_name').up(1).hide();
						if($('bank_name').hasClassName('required-entry')) $('bank_name').removeClassName('required-entry');
						$('name_account').up(1).hide();
						if($('name_account').hasClassName('required-entry')) $('name_account').removeClassName('required-entry');
						$('bank_country').up(1).hide();
						if($('bank_country').hasClassName('required-entry')) $('bank_country').removeClassName('required-entry');
						$('swift_bic').up(1).hide();
						if($('swift_bic').hasClassName('required-entry')) $('swift_bic').removeClassName('required-entry');
						$('account_number').up(1).hide();
						if($('account_number').hasClassName('required-entry')) $('account_number').removeClassName('required-entry');
						$('re_account_number').up(1).hide();
						if($('re_account_number').hasClassName('required-entry')) $('re_account_number').removeClassName('required-entry');
						$('payment_email').up(1).show();
						if(!$('payment_email').hasClassName('required-entry')) $('payment_email').addClassName('required-entry');
				
					};
				
				});
				
            });
            
            
        ";
    }

    public function getHeaderText()
    {   
        if( Mage::registry('affiliate_data_member_new') && Mage::registry('affiliate_data_member_new')->getId() ) {
            return Mage::helper('affiliate')->__("Edit Member '%s'", $this->htmlEscape(Mage::registry('affiliate_data_member')->getCustomerId()));
        } else {
            return Mage::helper('affiliate')->__('Add Member');
        }
    }
}