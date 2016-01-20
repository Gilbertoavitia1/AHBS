<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Program extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_arrayResult = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('Affiliate_member_program');
		$this->setUseAjax(true);
		$this->setEmptyText(Mage::helper('affiliate')->__('No program found'));
	}
	
  	protected function _prepareCollection()
  	{
  		$collection = Mage::helper('affiliate')->getMemberProgram($this->getRequest()->getParam('id'));
      	      	
	    $this->setCollection($collection);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
    	$this->addColumn('program_id', array(
        	'header'    => Mage::helper('affiliate')->__('ID'),
          	'align'     => 'center',
          	'width'     => '50px',
          	'index'     => 'program_id',
      	));
      	$this->addColumn('program_name', array(
          	'header'    => Mage::helper('affiliate')->__('Program Name'),
          	'align'     => 'left',
          	'index'     => 'program_name',
      	));
      	$this->addColumn('start_date', array(
			'header'    => Mage::helper('affiliate')->__('Start Date'),
      		'type'		=> 'datetime',	
			'width'     => '150px',
			'index'     => 'start_date',
      	));
      	$this->addColumn('end_date', array(
      		'header'    => Mage::helper('affiliate')->__('End Date'),
      		'type'		=> 'datetime',
      		'width'     => '150px',
      		'index'     => 'end_date',
      	));
      	$this->addColumn('total_commission', array(
      		'header'    => Mage::helper('affiliate')->__('Total Commission'),
      		'index'     => 'total_commission',
      	));
      	$this->addColumn('program_position', array(
      		'header'    => Mage::helper('affiliate')->__('Priority'),
      		'type'      => 'number',
      		'index'		=> 'program_position',
      		'align'		=> 'center'
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
      			'header'    => Mage::helper('affiliate')->__('Action'),
      			'width'     => '100',
      			'type'      => 'action',
      			'getter'    => 'getId',
      			'actions'   => array(
      						   		array(
      									'caption'   => Mage::helper('affiliate')->__('View'),
      									'url'       => array('base'=> '*/*/edit'),
      									'field'     => 'id'
      							)
      			),
      			'filter'    => false,
      			'sortable'  => false,
      			'index'     => 'stores',
      			'is_system' => true,
      		)
      	);
	  
      	return parent::_prepareColumns();
  	}
  	
  	protected function _filterStoreCondition($collection, $column) {
  		if(!$value = $column->getFilter()->getValue()) {
  			return;
  		}
  		$this->getCollection()->getSelect()->where("main_table.store_view like '%".$value."%' or main_table.store_view = '0'");
  	}
  
}