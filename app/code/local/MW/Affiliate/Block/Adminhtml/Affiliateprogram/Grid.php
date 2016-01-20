<?php
class MW_Affiliate_Block_Adminhtml_Affiliateprogram_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliateprogramGrid');
      	$this->setDefaultSort('program_id');
      	$this->setDefaultDir('DESC');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Affiliate Program Found'));
  	}

  	protected function _prepareCollection() {
    	$collections = Mage::getModel('affiliate/affiliateprogram')->getCollection();
      	$this->setCollection($collections);
      	return parent::_prepareCollection();
 	}

  	protected function _prepareColumns()
  	{
    	$this->addColumn('program_id', array(
        	'header'    => Mage::helper('affiliate')->__('ID'),
          	'align'     => 'right',
          	'width'     => '50px',
          	'index'     => 'program_id'
      	));
      	$this->addColumn('program_name', array(
        	'header'    => Mage::helper('affiliate')->__('Program Name'),
          	'align'     => 'left',
          	'index'     => 'program_name'
      	));
      	$this->addColumn('affiliate_commission', array(
      		'header'    => Mage::helper('affiliate')->__('Affiliate Commission'),
      		'align'     => 'center',
      		'index'     => 'commission'
      	));
      	$this->addColumn('customer_discount', array(
      		'header'    => Mage::helper('affiliate')->__('Customer Discount'),
      		'align'     => 'center',
      		'index'     => 'discount'
      	));
      	$this->addColumn('start_date', array(
			'header'    => Mage::helper('affiliate')->__('Start Date'),
			'width'     => '150px',
			'index'     => 'start_date'
      	));
      	$this->addColumn('end_date', array(
			'header'    => Mage::helper('affiliate')->__('End Date'),
			'width'     => '150px',
			'index'     => 'end_date',
      	));
	  	$this->addColumn('total_members', array(
          	'header'    => Mage::helper('affiliate')->__('Total Members'),
          	'align'     => 'left',
	  	  	'type'      => 'number',
          	'index'     => 'total_members',
      	));
      	$this->addColumn('total_commission', array(
          	'header'    => Mage::helper('affiliate')->__('Total Commission'),
          	'align'     => 'left',
      	  	'type'      => 'price',
          	'index'     => 'total_commission',
      	  	'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
      	$this->addColumn('program_position', array(
          	'header'    => Mage::helper('affiliate')->__('Priority'),
          	'align'     => 'left',
      	  	'type'      => 'number',
          	'index'     => 'program_position',
      	));
  		if(!Mage::app()->isSingleStoreMode()) {
        	$this->addColumn('store_view', array(
                'header'        => Mage::helper('affiliate')->__('Store View'),
                'index'         => 'store_view',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }
      	$this->addColumn('status', array(
        	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     => 'left',
          	'width'     => '80px',
          	'index'     => 'status',
          	'type'      => 'options',
          	'options'   => array(
            	1 => 'Enabled',
              	2 => 'Disabled',
          	),
      	));
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('affiliate')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('affiliate')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
    }
    
	protected function _filterStoreCondition($collection, $column) {
        if(!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->getSelect()->where("main_table.store_view like '%".$value."%' or main_table.store_view = '0'");
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('program_id');
        $this->getMassactionBlock()->setFormFieldName('affiliateprogramGrid');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('affiliate')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('affiliate')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('affiliate/statusprogram')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'		=> Mage::helper('affiliate')->__('Change status'),
             'url'  		=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' 	=> array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('affiliate')->__('Status'),
                         'values' => $statuses
                    )
             )
        ));
        return $this;
    }

  	public function getRowUrl($row) {
    	return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  	}

}