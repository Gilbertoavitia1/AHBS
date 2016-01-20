<?php

class Sparx_Storelocator_Block_Adminhtml_Storelocator_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('storelocatorGrid');
      $this->setDefaultSort('storelocator_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('storelocator/storelocator')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('storelocator_id', array(
          'header'    => Mage::helper('storelocator')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'storelocator_id',
      ));

      $this->addColumn('name', array(
         'header'     => Mage::helper('storelocator')->__('Store Name'),
         'align'      =>'left',
         'index'      => 'name',
         'width'      => '150px'
      ));
     
      $this->addColumn('longitude', array(
         'header'     => Mage::helper('storelocator')->__('Longitude'),
         'width'      => '250px',
         'index'      => 'longitude',
        ));
      
      $this->addColumn('latitude', array(
         'header'     => Mage::helper('storelocator')->__('latitude'),
         'width'      => '250px',
         'index'      => 'latitude',
        ));
      
      $this->addColumn('address', array(
         'header'     => Mage::helper('storelocator')->__('Address'),
         'width'      => '250px',
         'index'      => 'address',
        ));
     
      $this->addColumn('city', array(
	 'header'     => Mage::helper('storelocator')->__('City'),
	 'width'      => '130px',
	 'index'      => 'city',
	));
     
       $this->addColumn('state', array(
         'header'    => Mage::helper('storelocator')->__('State/Province'),
         'width'     => '130px',
         'index'     => 'state',
        ));
      
       $this->addColumn('country', array(
         'header'    => Mage::helper('storelocator')->__('Country'),
         'width'     => '130px',
         'index'     => 'country',
         'type'	     => 'options',
         'options'   => Mage::helper('storelocator')->getCountrieslist(),
        ));
      
       $this->addColumn('zipcode', array(
	 'header'    => Mage::helper('storelocator')->__('Zip Code'),
         'width'     => '130px',
	 'index'     => 'zipcode',
	));
       
       $this->addColumn('web_url', array(
	 'header'    => Mage::helper('storelocator')->__('Website Url'),
         'width'     => '130px',
	 'index'     => 'web_url',
	));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('storelocator')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('storelocator')->__('Status'),
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
                'header'    =>  Mage::helper('storelocator')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('storelocator')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('storelocator')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('storelocator')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('storelocator_id');
        $this->getMassactionBlock()->setFormFieldName('storelocator');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('storelocator')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('storelocator')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('storelocator/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('storelocator')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('storelocator')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}