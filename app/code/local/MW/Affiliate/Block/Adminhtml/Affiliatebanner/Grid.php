<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebanner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliatebannerGrid');
      	$this->setDefaultSort('banner_id');
     	$this->setDefaultDir('desc');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Affiliate Banner Found'));
  	}

  	protected function _prepareCollection()
  	{
    	$collections = Mage::getModel('affiliate/affiliatebanner')->getCollection();
      	$this->setCollection($collections);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{	
    	$this->addColumn('banner_id', array(
        	'header'    => Mage::helper('affiliate')->__('ID'),
          	'align'     => 'right',
          	'width'     => '50px',
          	'index'     => 'banner_id',
      	));
	   	$this->addColumn('image_name', array(
			'header'    => Mage::helper('affiliate')->__('Image'),
			'width'     => '75px',
			'index'     => 'image_name',
      		'filter'    => false,
            'sortable'  => false,
      		'renderer'  => 'affiliate/adminhtml_renderer_image',
      	));
      	$this->addColumn('title_banner', array(
          	'header'    => Mage::helper('affiliate')->__('Title'),
          	'align'     => 'left',
          	'index'     => 'title_banner',
      	));
      	$this->addColumn('link_banner', array(
          	'header'    => Mage::helper('affiliate')->__('Banner Link'),
         	'align'     => 'left',
          	'index'     => 'link_banner',
      	));
	 
      	$this->addColumn('width', array(
			'header'    => Mage::helper('affiliate')->__('Width (pixel)'),
			'width'     => '150px',
			'index'     => 'width',
      	));	
      	$this->addColumn('height', array(
			'header'    => Mage::helper('affiliate')->__('Height (pixel)'),
			'width'     => '150px',
			'index'     => 'height',
      	));
   		if (!Mage::app()->isSingleStoreMode()) {
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
  	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('affiliatebannerGrid');

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
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }
	protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        //$this->getCollection()->addFieldToFilter('store_view', array('like' => '%'.$value.'%'));
        $this->getCollection()->getSelect()->where("main_table.store_view like '%".$value."%' or main_table.store_view = '0'");
    }
  
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

   

}