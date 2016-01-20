<?php

class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'customer_id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliatemember';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Member'));
        $this->_removeButton('delete');
        //$this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Member'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $ajaxCheckReferralCodeUrl = Mage::helper('adminhtml')->getUrl('affiliate/adminhtml_affiliatemember/ajaxcheckreferralcode');
        $ajaxCheckReferralSponsorUrl = Mage::helper('adminhtml')->getUrl('affiliate/adminhtml_affiliatemember/ajaxcheckreferralsponsor');
        $ajaxCheckUniqueCustomUrlUrl = Mage::helper('adminhtml')->getUrl('affiliate/adminhtml_affiliatemember/ajaxcheckuniquecustomurl');
        $this->_formScripts[] = "
                $('customer_photo').hide();
        	$('referral_code').observe('change', function() {
				var memberId = $('customer_id').value,
					referralCode = $('referral_code').value;
					
				$('advice-validate-referral-code-referral_code').hide();
				
				new Ajax.Request(\"$ajaxCheckReferralCodeUrl\", {
					method: 'get',
					parameters: {member_id: memberId, referral_code: referralCode},
					onSuccess: function(transport) {
						response  = transport.responseText;
						
						if(response == '0') {
							$('advice-validate-referral-code-referral_code').show()
    					} else {
    						$('advice-validate-referral-code-referral_code').hide()
    					}
    				}
    			});
			});
                        
        	$('customer_url').observe('keydown', function(event) {
                 if(event.shiftKey)
    {
        event.preventDefault();
    }
    if (event.keyCode == 32)    {
        event.preventDefault();
    }
                
                });
        	$('referral_sponsor').observe('change', function() {
				var memberId = $('customer_id').value,
                                referralSponsor = $('referral_sponsor').value;
					
				$('advice-validate-referral-sponsor-referral_sponsor').hide();
				
				new Ajax.Request(\"$ajaxCheckReferralSponsorUrl\", {
					method: 'get',
					parameters: {member_id: memberId, referral_sponsor: referralSponsor},
					onSuccess: function(transport) {
						response  = transport.responseText;
						
						if(response == '0') {
							$('advice-validate-referral-sponsor-referral_sponsor').show()
    					} else {
    						$('advice-validate-referral-sponsor-referral_sponsor').hide()
    					}
    				}
    			});
			});	
                        
            $('customer_url').observe('change', function() {
		var memberId = $('customer_id').value,
		customer_url = $('customer_url').value;
					
                $('advice-validate-customer-url-unique').hide();
				
                new Ajax.Request(\"$ajaxCheckUniqueCustomUrlUrl\", {
                        method: 'get',
                        parameters: {member_id: memberId, customer_url: customer_url},
                        onSuccess: function(transport) {
                        response  = transport.responseText;
						
                        if(response == '0') {
                            $('advice-validate-customer-url-unique').show()
                        } else {
                            $('advice-validate-customer-url-unique').hide()
                        }
                    }
                });
            });
                        
        		
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

            
            document.observe('dom:loaded', function () {
            	error = '<div style=\"display:none\" id=\"advice-validate-referral-code-referral_code\" class=\"validation-advice\">This referral code is not available</div>';
				$('referral_code').up('td').insert({bottom: error});
                                
            	error = '<div style=\"display:none\" id=\"advice-validate-referral-sponsor-referral_sponsor\" class=\"validation-advice\">This referral sponsor is not available</div>';
				$('referral_sponsor').up('td').insert({bottom: error});
                                
            	error = '<div style=\"display:none\" id=\"advice-validate-customer-url-unique\" class=\"validation-advice\">This url its already in use</div>';
				$('customer_url').up('td').insert({bottom: error});
                

                                /* CUSTOM PHOTO */
                _varImg = $('customer_photo').value ? $('customer_photo').value : $('normal_photo').value;                
                file_ = '<form role=\"form\" id=\"nodeForm\"><i class=\"imageProgress\"></i><img src=\"' + _varImg + '\" class=\"personPhoto img-responsive img-rounded\" height=100><br><div class=\"uploadControl\"></div></form>';
                $('customer_photo').up('td').insert({bottom: file_});               
                
                         /**/       
            
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
            

            jQuery.noConflict();
jQuery(function ($) {
     $('customer_photo').val('url');
    _memberAffiliate = {
        vars: {
            form:'#nodeForm',            
            iconImage:\"images/user.jpg\",
            thumbImg: '.personPhoto',
            uploadImg:\".uploadControl\", 
            cloud_name: \"www-habitossaludables-com-mx\", 
            preset:'wjckss4q', 
            progressControl:'.imageProgress',
            imageInput: 'input[name=customer_photo]',
        },
        init:function() {
            $(_memberAffiliate.vars.uploadImg).append(
            $.cloudinary.unsigned_upload_tag(_memberAffiliate.vars.preset,
                {cloud_name: _memberAffiliate.vars.cloud_name })
                .bind('cloudinarystart', _memberAffiliate.private.development.helpers.ImageUploadStart)
                .bind('cloudinarydone', _memberAffiliate.private.development.helpers.ImageUploadComplete)
                .bind('cloudinaryprogress', _memberAffiliate.private.development.helpers.ImageUploadProgress));
        },
        private: {
            development: {
                helpers: {
                    ImageUploadStart:function(e, data) {
                        $(_memberAffiliate.vars.progressControl).html('0%');
                    },
                    ImageUploadComplete:function(e, data) {
                        url = $.cloudinary.url(data.result.public_id, {
                            format: 'jpg', width: 130, height: 150, cloud_name: _memberAffiliate.vars.cloud_name,
                            crop: 'thumb', gravity: 'face', effect: 'saturation:50'
                        });
                        
                       $(_memberAffiliate.vars.imageInput).attr('value',url)
                        $(_memberAffiliate.vars.thumbImg).attr('src', url);

                        $(_memberAffiliate.vars.progressControl).html('100%');
                        setTimeout(function() {
                            $(_memberAffiliate.vars.progressControl).html('');}, 2000);

                    },
                    ImageUploadProgress:function(e, data) {
                        $(_memberAffiliate.vars.progressControl).html(Math.round((data.loaded * 100.0) / data.total) + '%');
                    }
                }
            }
        }
}

    _memberAffiliate.init();


});

            
        ";
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