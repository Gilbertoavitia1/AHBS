<?php
class MW_Affiliate_Block_Adminhtml_Affiliatecommissionholding_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliatecommissionholdingGrid');
      	//$this->setDefaultSort('affiliate__id');
      	//$this->setDefaultDir('desc');
      	//$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Commission Holding Found'));
  	}

  	protected function _prepareCollection()
  	{
  		$customer_table = Mage::getModel('core/resource')->getTableName('customer/entity');
  		
      	$collections = Mage::getModel('affiliate/affiliatehistory')
      				   ->getCollection()
      				   ->addFieldToFilter('status', array('eq' => MW_Affiliate_Model_Status::HOLDING))
      				   ->setOrder('history_id', 'desc');
      	
      	$collections->getSelect()
      			    ->join(array('customer_entity' => $customer_table), 'main_table.customer_invited = customer_entity.entity_id', array('email' => 'customer_entity.email'));
      	
	    $this->setCollection($collections);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
    	$this->addColumn('history_id', array(
        	'header'    => Mage::helper('affiliate')->__('ID'),
          	'align'     => 'center',
          	'width'     => '50px',
          	'index'     => 'history_id',
      	));
      	$this->addColumn('affiliate_email', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Email'),
          	'align'     => 'left',
          	'index'     => 'email',
      	));
      	$this->addColumn('order_id', array(
          	'header'    => Mage::helper('affiliate')->__('Order'),
          	'align'     => 'left',
          	'index'     => 'order_id',
      	));
      	$this->addColumn('purchase_total', array(
      		'header'    	=> Mage::helper('affiliate')->__('Purchase Total'),
      		'align'     	=> 'left',
      		'type'			=> 'price',
      		'currency_code'	=> Mage::app()->getBaseCurrencyCode(),
      		'index'     	=> 'total_amount',
      	));
      	$this->addColumn('commission', array(
      		'header'    	=> Mage::helper('affiliate')->__('Commission'),
      		'align'     	=> 'left',
      		'type'			=> 'price',
      		'currency_code'	=> Mage::app()->getBaseCurrencyCode(),
      		'index'     	=> 'history_commission',
      	));
      	$this->addColumn('discount', array(
      		'header'    	=> Mage::helper('affiliate')->__('Discount'),
      		'align'     	=> 'left',
      		'type'			=> 'price',
      		'currency_code'	=> Mage::app()->getBaseCurrencyCode(),
      		'index'     	=> 'history_discount'
      	));
      	$this->addColumn('beginning_time', array(
      		'header'    	=> Mage::helper('affiliate')->__('Beginning Time'),
      		'align'     	=> 'left',
      		'type'			=> 'datetime',	
      		'index'     	=> 'transaction_time'
      	));
      	$this->addColumn('ending_time', array(
      		'header'    	=> Mage::helper('affiliate')->__('Ending Time'),
      		'align'     	=> 'left',
      		'type'			=> 'datetime',
      		'renderer'		=> 'affiliate/adminhtml_renderer_commissionholdingendtime'	
      	));
      	
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
  	}
  
}