<?php

class MW_Affiliate_Block_Adminhtml_Affiliategroup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliategroupGrid');
      $this->setDefaultSort('group_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      $this->setEmptyText(Mage::helper('affiliate')->__('No Affiliate Group Found'));
  }
  protected function _prepareCollection()
  {   

      $collection = Mage::getModel('affiliate/affiliategroup')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  private function _getNameArray()
    {
    	$arr = array();
    	$collection = Mage::getModel('customer/customer')->getCollection()->addNameToSelect();
		foreach($collection as $item){
			$arr[$item->getId()] = $item->getName();
		}
        return $arr;
    }

  protected function _prepareColumns()
  {   
      $this->addColumn('group_id', array(
          'header'    => Mage::helper('affiliate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'group_id',
      ));

      $this->addColumn('group_name', array(
          'header'    => Mage::helper('affiliate')->__('Group Name'),
          'align'     =>'left',
          'index'     => 'group_name',
      ));
       $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('affiliate')->__('Action'),
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
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      return parent::_prepareColumns();
  }
   protected function _prepareMassaction()
    {
        $this->setMassactionIdField('affiliategroupGrid');
        $this->getMassactionBlock()->setFormFieldName('group_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('affiliate')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('affiliate')->__('Are you sure?')
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}