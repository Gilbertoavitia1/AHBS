<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebonuspreferred_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('affiliatebonus_preferredcustomer');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('Bonus not found'));
    }

    protected function _prepareCollection()
    {
    	$resource = Mage::getModel('core/resource');
        
      	$collection = Mage::getModel('affiliate/affiliatebonus')
            ->getCollection()
            ->addFieldToFilter('bonus_id', array('eq' => 2));
                
        $collection->addExpressionFieldToSelect('total','sum(total)','total_amount_sum');

        $collection->getSelect()->group(array('parent_id'));
        
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
    }
        

    protected function _prepareColumns()
    {
    	$this->addColumn('id', array(
            'header'    => Mage::helper('affiliate')->__('# ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'parent_id',
      	));
        
      	$this->addColumn('referral_name', array(
            'header'    => Mage::helper('affiliate')->__('Affiliate Parent Sponsor'),
            'align'     =>'left',
            'renderer'  => 'affiliate/adminhtml_renderer_name',
            'index'     => 'parent_id',
     	));
        
      	$this->addColumn('affiliate_child', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Preferred Child'),
          	'align'     =>'left',
          	'index'     => 'child_id',
          	'renderer'  => 'affiliate/adminhtml_renderer_namechild',
     	));
        
      	$this->addColumn('start_period', array(
          	'header'    => Mage::helper('affiliate')->__('Start Period'),
          	'align'     =>'left',
          	'index'     => 'start_day',
                'type'      => 'datetime',
                'format'    => 'Y/MM/d'
     	));
        
      	$this->addColumn('end_period', array(
          	'header'    => Mage::helper('affiliate')->__('End Period'),
          	'align'     =>'left',
          	'index'     => 'end_date',
                'type'      => 'datetime',
                'format'    => 'Y/MM/d'
     	));
        
      	$this->addColumn('total', array(
          	'header'    => Mage::helper('affiliate')->__('Total'),
          	'align'     =>'left',
          	'index'     => 'total',
                'type'      => 'price',
                'currency_code' => Mage::app()->getStore($row['store_id'])->getCurrentCurrencyCode()//Mage::app()->getBaseCurrencyCode(),
     	));
        
        $this->addColumn('reference', array(
            'header'    => Mage::helper('affiliate')->__('Reference'),
            'align'     =>'left',
            'index'     => 'reference',
        ));
                
      	
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
        
        return parent::_prepareColumns();
    }
    
    
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/viewPreferred', array('id' => $row->getParentId(), 'date' => strtotime($row->getStartDay())));
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