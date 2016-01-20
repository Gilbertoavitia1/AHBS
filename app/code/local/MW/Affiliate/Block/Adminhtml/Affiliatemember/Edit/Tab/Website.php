<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Website extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_arrayResult = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('Affiliate_member_website');
		$this->setUseAjax(true);
		$this->setEmptyText(Mage::helper('affiliate')->__('No website found'));
	}
	
  	protected function _prepareCollection()
  	{
  		$customer_table = Mage::getModel('core/resource')->getTableName('customer/entity');
      	$collections = Mage::getModel('affiliate/affiliatewebsitemember')
      				   ->getCollection()
      				   ->addFieldToFilter('customer_id', array('eq' => $this->getRequest()->getParam('id')));
      				   //->setOrder('status', 'ASC');
      	
      	$collections->getSelect()
      				->join(array('customer_entity' => $customer_table), 'main_table.customer_id = customer_entity.entity_id', array('email' => 'customer_entity.email'));
      	
	    $this->setCollection($collections);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
    	$this->addColumn('affiliate_website_id', array(
        	'header'    => Mage::helper('affiliate')->__('ID'),
          	'align'     => 'center',
          	'width'     => '50px',
          	'index'     => 'affiliate_website_id',
      	));
      	$this->addColumn('website', array(
          	'header'    => Mage::helper('affiliate')->__('Website'),
          	'align'     => 'left',
          	'index'     => 'domain_name',
      	));
	 
      	$this->addColumn('status', array(
			'header'    => Mage::helper('affiliate')->__('Status'),
			'width'     => '150px',
			'index'     => 'status',
      		'type'      => 'options',
      		'options'   => array(
      			1 => 'Verified',
      			0 => 'Not Verified',
      		),
      	));
	  
      	return parent::_prepareColumns();
  	}
  
}