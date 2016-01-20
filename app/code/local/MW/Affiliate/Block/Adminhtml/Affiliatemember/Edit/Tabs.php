<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliatemember_tabs');
      	$this->setDestElementId('edit_form');
      	$this->setTitle(Mage::helper('affiliate')->__('Affiliate Member Information'));
  	}

  	protected function _beforeToHtml()
  	{   
  		$order_id = $this->getRequest()->getParam('orderid');
  	  	if(isset($order_id)){
  	  		$content = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_viewtransaction')->toHtml();
  	  	} 
  	  	else{
  	  		$content = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_transaction')->toHtml();
  	  	}

      	$this->addTab('form_member_detail', array(
        	'label'     => Mage::helper('affiliate')->__('General information'),
          	'title'     => Mage::helper('affiliate')->__('General information'),
          	'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_form')->toHtml(),
      	  	'active'    => !isset($order_id)?true:false,
      	));
      	$this->addTab('form_member_credit', array(
           	'label'     => Mage::helper('affiliate')->__('Manual Adjustment/Payout'),
           	'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_credit')->toHtml()
      	));
      	
      	$this->addTab('form_member_credit_history', array(
          	'label'     => Mage::helper('affiliate')->__('Transaction History'),
          	'title'     => Mage::helper('affiliate')->__('Transaction History'),
          	'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_credithistory')->toHtml(),
     	));
     	
      	$this->addTab('form_member_invitation', array(
          	'label'     => Mage::helper('affiliate')->__('Invitation History'),
          	'title'     => Mage::helper('affiliate')->__('Invitation History'),
          	'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_invitation')->toHtml(),
        ));
        
      	$this->addTab('form_member_withdrawn', array(
          	'label'     => Mage::helper('affiliate')->__('Withdrawal History'),
          	'title'     => Mage::helper('affiliate')->__('Withdrawal History'),
          	'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_withdrawn')->toHtml(),
     	));

      	$this->addTab('form_member_transaction', array(
          	'label'     => Mage::helper('affiliate')->__('Commission History'),
          	'title'     => Mage::helper('affiliate')->__('Commission History'),
          	'content'   => $content,
      	  	'active'    => !isset($order_id)?false:true,
     	));
     	
      	$this->addTab('form_member_program', array(
      		'label'		=> Mage::helper('affiliate')->__('Affiliate Programs'),
      		'title'		=> Mage::helper('affiliate')->__('Affiliate Programs'),
      		'content'	=> $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_program')->toHtml(),			
      	));
      	
      	$this->addTab('form_member_website', array(
      		'label'     => Mage::helper('affiliate')->__('Affiliate Websites'),
      		'title'     => Mage::helper('affiliate')->__('Affiliate Websites'),
      		'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_website')->toHtml(),
      	));
      	$this->addTab('form_member_network', array(
      		'label'     => Mage::helper('affiliate')->__('Affiliate Network'),
      		'title'     => Mage::helper('affiliate')->__('Affiliate Network'),
      		'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_network')->toHtml(),
      	));
        
      	$this->addTab('form_member_binary', array(
      		'label'     => Mage::helper('affiliate')->__('Affiliate Binary'),
      		'title'     => Mage::helper('affiliate')->__('Affiliate Binary'),
      		'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_binary')->toHtml(),
      	));
        
      	$this->addTab('form_member_binaryregistry', array(
      		'label'     => Mage::helper('affiliate')->__('Binary Registry'),
      		'title'     => Mage::helper('affiliate')->__('Binary Registry'),
      		'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_binaryregistry')->toHtml(),
      	));
    
      	return parent::_beforeToHtml();
    }
}