<?php
class MW_Affiliate_Block_Adminhtml_Affiliateparent_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setDefaultSort('customer_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Customer Found'));
    }
  
    protected function _prepareCollection()
    {
        $collection_banner = Mage::getModel('affiliate/affiliatebanner')->getCollection();
        $customer_table = $collection_banner->getTable('affiliatecustomers');
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email');
        
        $collection->getSelect()->joinLeft(
                array('customer_affiliate'=>$customer_table),
                'e.entity_id = customer_affiliate.customer_id',
                array('*'));
           
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
  
    protected function _prepareColumns()
    {   
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'      => 'number',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Customer Name'),
            'index'     => 'name'
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Customer Email'),
            'index'     => 'email'
        ));

        $this->addColumn('phone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'index'     => 'entity_id',
            'renderer'  => 'affiliate/adminhtml_renderer_telephone',
        ));

        $this->addColumn('referral_sponsor', array(
            'header'    => Mage::helper('affiliate')->__('# Patrocinador'),
            'align'     =>'left',
            'index'     => 'referral_sponsor',
            'filter_condition_callback' => array($this, '_filterCustomerSponsorCondition'),
            'renderer'  => 'affiliate/adminhtml_renderer_parentsponsor',
        ));
        
        $groups = array();
      	$collection_groups = Mage::getModel('affiliate/affiliategroup')->getCollection();
      	foreach ($collection_groups as $g) {
            $groups[$g->getGroupId()] = $g->getGroupName();
      	}
      
      	$this->addColumn('group_id', array(
        	'header'    	=> Mage::helper('affiliate')->__('Affiliate Group'),
          	'align'     	=> 'left',
          	'index'     	=> 'group_id',
          	'width'	  		=>  150,
      	  	'renderer'  	=> 'affiliate/adminhtml_renderer_affiliategroup',
          	'type' 			=> 'options',
          	'options' 		=> $groups,
      	  	'filter_condition_callback' => array($this, '_filterGroupCondition'),
      	));
        
//        $this->addColumn('referral_name', array(
//            'header'    => Mage::helper('affiliate')->__('Affiliate Name'),
//            'align'     =>'left',
//            'index'     => 'customer_invited',
//            'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
//            'renderer'  => 'affiliate/adminhtml_renderer_parentname',
//        ));
  
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));

        return parent::_prepareColumns();
    }
    
    
    protected function _filterGroupCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
       $customer_ids = array();
       $customer_collections =  Mage::getModel('affiliate/affiliategroupmember')->getCollection()->addFieldToFilter('group_id',array('eq' => $value));
       foreach ($customer_collections as $customer_collection) {
       		$customer_ids[] = $customer_collection->getCustomerId();
       }
       $this->getCollection()->getSelect()->where("customer_affiliate.customer_id in (?)",$customer_ids);
    }
    
    protected function _filterReferralnameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $customer_ids = array();
        $value = '%'.$value.'%';
       // $customer_collections =  Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('firstname',array('like' => $value));
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
        $this->getCollection()->getSelect()->where("customer_affiliate.customer_invited in (?)",$customer_ids);
    }
    
    protected function _filterCustomerSponsorCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
            
        $this->getCollection()->getSelect()->where("customer_affiliate.referral_sponsor in (?)",$column->getFilter()->getValue());
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customerGrid');

        $this->getMassactionBlock()->addItem('parent_affiliate', array(
             'label'=> Mage::helper('affiliate')->__('Change Affiliate Parent'),
             'url'  => $this->getUrl('*/*/massParentAffiliate', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'parent_affiliate',
                         'type' => 'text',
                         'class' => 'required-entry validate-email',
                         'label' => Mage::helper('affiliate')->__('Affiliate Parent'),
                     )
             )
        ));
        return $this;
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

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $col_id =>$column) {
                if (!$column->getIsSystem()) {
                	if($col_id == 'email')
                    {   
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getEmail()).'"';
                    	//zend_debug::dump($item->getOrderId());die();
                    }
                    else
                    {
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($item)).'"';
                    }
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
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