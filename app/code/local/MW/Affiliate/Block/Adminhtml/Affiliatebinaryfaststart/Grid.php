<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinaryfaststart_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('affiliatebinary_faststart');
        $this->setDefaultSort('start_period');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('Bonus not found'));
    }

    protected function _prepareCollection()
    {        
      	$collection = Mage::getModel('affiliate/affiliatebinaryfaststart')->getCollection();
        
//        $collection->getSelect()->order(array('main_table.start_date DESC', 'paid DESC', 'customer_id ASC'));
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
    }
        

    protected function _prepareColumns()
    {
    	$this->addColumn('id', array(
            'header'    => Mage::helper('affiliate')->__('#'),
            'align'     => 'right',
            'width'     => '25px',
            'index'     => 'id',
      	));
        
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('affiliate')->__('Order'),
            'align'     => 'right',
            'width'     => '25px',
            'index'     => 'increment_id',
      	));
        
        $this->addColumn('affiliate_name', array(
            'header'    => Mage::helper('affiliate')->__('Name'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '250px',
            'renderer'  => 'affiliate/adminhtml_renderer_name',
            'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
     	));
        
        $this->addColumn('affiliate_email', array(
            'header'    => Mage::helper('affiliate')->__('Email'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '200px',
            'renderer'  => 'affiliate/adminhtml_renderer_emailreferral',
            'filter_condition_callback' => array($this, '_filterReferralemailCondition'),
     	));
        
        $this->addColumn('group_name', array(
            'header'    => Mage::helper('affiliate')->__('Group Name'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'filter'    => false,
            'renderer'  => 'affiliate/adminhtml_renderer_affiliategroup',
     	));
        
        $this->addColumn('start_period', array(
          	'header'    => Mage::helper('affiliate')->__('Start Period'),
          	'align'     =>'left',
                'width'     => '25px',
          	'index'     => 'start_date',
                'type'      => 'datetime',
                'format'    => 'Y/MM/dd',
     	));
        
      	$this->addColumn('end_period', array(
          	'header'    => Mage::helper('affiliate')->__('End Period'),
          	'align'     =>'left',
                'width'     => '25px',
          	'index'     => 'end_date',
                'type'      => 'datetime',
                'format'    => 'Y/MM/dd'
     	));
                
         $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('affiliate')->__('View Report'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getIncrementId',
                'actions'   => array(
                    array(
                        'caption'   => 'view',
                        'url'       => array('base'=> '*/*/editfaststart'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));
                
      	
        $this->addExportType('*/*/exportbinaryresidualCsv', Mage::helper('affiliate')->__('CSV'));
        $this->addExportType('*/*/exportbinaryresidualXml', Mage::helper('affiliate')->__('XML'));
        
        return parent::_prepareColumns();
    }
    
    protected function _filterReferralnameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) { return; }
        $customer_ids = array();
        $value = '%'.$value.'%';
        $customer_collections =  Mage::getModel('customer/customer')->getCollection()
                 ->addAttributeToFilter(array(
                     array(
                         'attribute' => 'firstname',
                         array('like' => $value),
                         ),
                     array(
                         'attribute' => 'lastname',
                         array('like' => $value),
                         ),
                     ));

        foreach ($customer_collections as $customer_collection) {
                 $customer_ids[] = $customer_collection->getId();
        }

        $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$customer_ids);

//        if (!$value = $column->getFilter()->getValue()) return;
//        
//        $customer_ids = array();
//        
//        $fullname = explode(" ", $value);        
//        $search = [];
//        foreach ($fullname as $key => $value) {
//            $search[]=array(
//                'attribute' => 'firstname',
//                array('like' => $value),
//                );           
//            $search[]=array(
//                'attribute' => 'lastname',
//                array('like' => $value),
//                );            
//        }
//            
//        $customer_collections =  Mage::getModel('customer/customer')->getCollection()
//                ->addAttributeToFilter($search);
//        
//        foreach ($customer_collections as $customer_collection) {
//       		$customer_ids[] = $customer_collection->getId();
//        }
//        
//        $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$customer_ids);
    }
    
    
    
    protected function _filterReferralemailCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $customer_ids = array();
        $value = '%'.$value.'%';

        $customer_collections =  Mage::getModel('customer/customer')->getCollection()
                 ->addAttributeToFilter(array(
                     array(
                         'attribute' => 'email',
                         array('like' => $value),
                         )));
        foreach ($customer_collections as $customer_collection) {
                 $customer_ids[] = $customer_collection->getId();
        }
        
        $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$customer_ids);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/editresidual', array('id' => $row->getId()));
    }
    
    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach($this->getCollection() as $item) 
        {
            $data = array();
            foreach($this->_columns as $col_id =>$column) 
            {
                if(!$column->getIsSystem()) 
                {
                	if($col_id == 'email')
                    {   
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getEmail()).'"';
                    }
                    else
                    {
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($item)).'"';
                    }
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if($this->getCountTotals())
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($this->getTotals())).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

}