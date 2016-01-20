<?php

class MW_Affiliate_Block_Adminhtml_Affiliatewebsite_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliatewebsiteGrid');
      	$this->setDefaultSort('affiliate_website_id');
      	$this->setDefaultDir('desc');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Website Found'));
  	}

  	protected function _prepareCollection()
  	{
  		$customer_table = Mage::getModel('core/resource')->getTableName('customer/entity');
  		
      	$collections = Mage::getModel('affiliate/affiliatewebsitemember')->getCollection();
      	
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
      	$this->addColumn('customer_id', array(
          	'header'    => Mage::helper('affiliate')->__('Customer Email'),
          	'align'     => 'left',
          	'index'     => 'email',
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
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
  	}
  
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('affiliate_website_id');
        $this->getMassactionBlock()->setFormFieldName('affiliatewebsiteGrid');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('affiliate')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('affiliate')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('affiliate/statusprogram')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('affiliate')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('affiliate')->__('Status'),
                         'values' => array(
							1 => 'Verified',
							0 => 'Not Verified',
							),
                     )
             )
        ));
        return $this;
    }
	
	//public function getRowUrl($row)
	//{
	  //return $this->getUrl('*/*/edit', array('id' => $row->getAffiliateWebsiteId()));
	//}
}