<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Binary extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_arrayResult = array();
	protected $array_network_table = array();

 	public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_member_binary');
        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Sub-affiliate found'));
    }
	public function getGridUrl()
    {
    	return $this->getUrl('affiliate/adminhtml_affiliatemember/binary', array('id'=>$this->getRequest()->getParam('id')));
    }
    
    protected function _prepareCollection()
    {
        $this->_arrayResult = Mage::helper('affiliate_binary')->binaryNetwork($this->getRequest()->getParam('id'), '', null, 0);
        $collection = new Varien_Data_Collection();
        $i = 1;
        foreach ($this->_arrayResult as $row) {
            $rowObj = new Varien_Object();
            $rowObj->setData($row);
            $collection->addItem($rowObj);
            $i = $i + 1;
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
            
    protected function _prepareColumns()
    {
        $this->addColumn('level', array(
            'header'    => Mage::helper('affiliate')->__('Level'),
            'align'     => 'center',
            'index'     => 'level',
        	'width'		=> '10',
        ));
        
        $this->addColumn('name', array(
            'header'    =>  Mage::helper('affiliate')->__('Name'),
            'align'     =>  'left',
            'index'     =>  'name',
        ));
      	
      	$this->addColumn('Email', array(
          	'header'    => Mage::helper('affiliate')->__('Email'),
          	'index'     => 'email',
      	));
        
//        $this->addColumn('commission', array(
//            'header'    	=>  Mage::helper('affiliate')->__('Commission'),
//            'index'     	=>  'commission',
//			'type'      	=>  'price',
//			'currency_code' =>  Mage::app()->getBaseCurrencyCode(),
//        ));
//        
        $this->addColumn('referred_by', array(
        	'header'    	=> Mage::helper('affiliate')->__('Referred by'),
          	'align'     	=> 'left',
          	'index'     	=> 'referral'
      	));
        
        $this->addColumn('status', array(
          	'header'    	=> Mage::helper('affiliate')->__('Status'),
          	'align'     	=> 'center',
          	'index'     	=> 'status',
        	'width'		=> '10'
      	));
        
        $this->addColumn('joined_date', array(
            'header'    =>  Mage::helper('affiliate')->__('Joinded Date'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'joined_date',
            'gmtoffset' => true,
            'default'   =>  '----'
        ));
        
        
      	return parent::_prepareColumns();
    }  	 
}
