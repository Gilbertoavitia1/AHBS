<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Invitation extends Mage_Adminhtml_Block_Widget_Grid
{
 	public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_member_invitation');
        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Invitation History Found'));
    }
    
	public function getGridUrl() {
    	return $this->getUrl('affiliate/adminhtml_affiliatemember/invitation', array('id'=>$this->getRequest()->getParam('id')));
    }
	
	protected function _prepareCollection()
 	{
    	$collection = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
      				->addFieldToFilter('customer_id', array('eq' => $this->getRequest()->getParam('id')))
      				->setOrder('invitation_time', 'DESC');
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
    	$this->addColumn('invitation_id', array(
        	'header'    => Mage::helper('affiliate')->__('ID'),
          	'align'     => 'right',
          	'width'     => '50px',
          	'index'     => 'invitation_id',
      	));

      	$this->addColumn('invitation_time', array(
			'header'    => Mage::helper('affiliate')->__('Invitation Time'),
      		'type'      => 'datetime',
			'width'     => '150px',
			'index'     => 'invitation_time',
      		'gmtoffset' => true,
      		'default'   => ' ---- '
      	));
	 	$this->addColumn('email', array(
          	'header'    => Mage::helper('affiliate')->__('Customer Email Address'),
          	'align'     => 'left',
          	'index'     => 'email',
      	));
      	$this->addColumn('ip', array(
          	'header'    => Mage::helper('affiliate')->__('Ip Address'),
          	'align'     => 'left',
          	'index'     => 'ip',
      	));
      	$this->addColumn('status', array(
          	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     => 'left',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('affiliate/statusinvitation')->getOptionArray(),
      	));
	  
      	return parent::_prepareColumns();
  	}
   
}
