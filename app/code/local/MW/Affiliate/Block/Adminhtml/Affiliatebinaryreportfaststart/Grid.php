<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinaryreportfaststart_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('affiliatebinaryreport_faststart');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No customers available'));
    }

    protected function _prepareCollection()
    {   
        $range = Mage::helper('affiliate_binary')->getRangeDate();
        $historial = Mage::getResourceModel('sales/order_status_history_collection')
            ->addAttributeToSelect('parent_id')
            ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
            ->addAttributeToFilter('main_table.created_at', array('from'=>$range[0], 'to'=>$range[1]))
            ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')));
        
        $sale = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
            ->addAttributeToFilter('affiliate_network', array('eq' => 2))
            ->addAttributeToFilter('affiliate_sale_type', array('nin' => 3));
        
        $package = explode(",", Mage::getStoreConfig('binary/faststart/products', 0));
        $total_sale = array();
        foreach ($sale as $order) { 
            $items = $order->getAllVisibleItems();  
            $spoints = 0;
            foreach($items as $item){
                $single = $item->getData(); 
                if ($single['product_type'] == 'bundle'){
                    if (array_search($single['sku'], $package)){
                        array_push($total_sale, $order->getEntityId());
                    }
                }
            }
        }
        
        $collection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => $total_sale));
        
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {        
    	$this->addColumn('increment_id', array(
            'header'    => Mage::helper('affiliate')->__('# Order'),
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
        
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('affiliate')->__('Email'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '200px',
            'renderer'  => 'affiliate/adminhtml_renderer_emailreferral',
            'filter_condition_callback' => array($this, '_filterReferralemailCondition'),
     	));
        
        $this->addColumn('levels_to', array(
            'header'    => Mage::helper('affiliate')->__('Levels to Pay'),
            'align'     =>'center',
            'index'     => 'customer_id',
            'width'     => '20px',
            'renderer'  => 'affiliate/adminhtml_renderer_rfslevels',
     	));        
        $this->addColumn('levels_people', array(
            'header'    => Mage::helper('affiliate')->__('Levels'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '250px',
            'renderer'  => 'affiliate/adminhtml_renderer_rfslevelspeople',
     	));        
        
        $this->addColumn('group_name', array(
            'header'    => Mage::helper('affiliate')->__('Group Name'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'filter'    => false,
            'renderer'  => 'affiliate/adminhtml_renderer_affiliategroup',
     	));
//        
        $this->addColumn('affiliate_report', array(
            'header'    => '',
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '200px',
            'filter'    => false,
            'renderer'  => 'affiliate/adminhtml_renderer_faststartreport',
     	));
//                
//      	
//        $this->addExportType('*/*/exportbinaryresidualCsv', Mage::helper('affiliate')->__('CSV'));
//        $this->addExportType('*/*/exportbinaryresidualXml', Mage::helper('affiliate')->__('XML'));
//        
        return parent::_prepareColumns();
    }
    
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_id');
        $this->getMassactionBlock()->setFormFieldName('affiliatebinaryreport_faststart');
        
        $this->getMassactionBlock()->addItem('run_report', array(
            'label'=> Mage::helper('affiliate')->__('Run Report'),
            'url'  => $this->getUrl('*/*/massRunReportFaststart', array('_current'=>true)),
        ));
        
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return '';//$this->getUrl('*/*/editresidual', array('id' => $row->getId()));
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