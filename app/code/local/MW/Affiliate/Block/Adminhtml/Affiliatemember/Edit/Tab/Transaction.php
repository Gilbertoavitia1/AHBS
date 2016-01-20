<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Transaction extends Mage_Adminhtml_Block_Widget_Grid
{

 	public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_member_transaction');
       // $this->setDefaultSort('transaction_time');
       // $this->setDefaultDir('desc');

        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Commission History Found'));
    }
	public function getGridUrl()
    {
    	return $this->getUrl('affiliate/adminhtml_affiliatemember/transaction', array('id'=>$this->getRequest()->getParam('id')));
        
    }
	protected function _prepareCollection()
  	{
  		/*
  		$collection = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
			  		  ->addFieldToFilter('customer_id', array('eq' => $this->getRequest()->getParam('id')))
			  		  ->addFieldToFilter('commission', array('gt' => '0'))
			  		  ->setOrder('invitation_time', 'DESC');
  		
  		$this->setCollection($collection);
  		return parent::_prepareCollection();
  		*/
  	
      	$collection = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
      					->addFieldToFilter('customer_invited',$this->getRequest()->getParam('id'))
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
      	
  	}
  	
  	protected function _prepareColumns()
  	{
  		$this->addColumn('history_id', array(
  			'header'    => Mage::helper('affiliate')->__('ID'),
  			'align'     => 'center',
  			'width'     => '50px',
  			'index'     => 'history_id',
  		));
  	
  		$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
      		'width'		=>  150,
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));
  		
  		$this->addColumn('commission_type', array(
  			'header'    => Mage::helper('affiliate')->__('Commission Type'),
  			'align'     => 'left',
  			'index'     => 'commission_type',
  			'type'      => 'options',
  			'options'   => Mage::getModel('credit/transactiontype')->getAffiliateTypeArray()
  		));
  		
  		$this->addColumn('total_commission', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Commission'),
          	'index'     => 'total_commission',
      		'width'		=>  '90',
      		'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
	  
      
		$this->addColumn('total_discount', array(
            'header'    =>  Mage::helper('affiliate')->__('Customer Discount'),
        	'align'     =>  'center',
			'width'		=>  '90',
            'index'     =>  'total_discount',
			'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        
        $this->addColumn('grand_total', array(
	          'header'    => Mage::helper('affiliate')->__('Purchase Total'),
	          'align'     =>'right',
	          'index'     => 'grand_total',
	          'width'		=>  '100',
	          'type'      =>  'price',
	          'currency_code' => Mage::app()->getBaseCurrencyCode(),
	      	  'renderer'  => 'affiliate/adminhtml_renderer_purchasehistory',
	          'filter' => false,
	          'sortable'  => false,
      	));
  		
  		$this->addColumn('detail', array(
  			'header'    => Mage::helper('affiliate')->__('Detail'),
  			'align'     => 'left',
  			'renderer'	=> 'affiliate/adminhtml_renderer_affiliatetransaction'
  		));
  		
  		$this->addColumn('status', array(
          	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     =>'center',
        	'width'		=>  '100',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('affiliate/status')->getOptionArray(),
      	));
  		
  		$this->addColumn('action', array(
            'header'    =>  Mage::helper('affiliate')->__('Action'),
            'width'     => '60',
      		'align'		=> 'center',	
            'type'      => 'action',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
     		'renderer'	=> 'affiliate/adminhtml_renderer_transactionmemberaction'
        ));
  		 
  		return parent::_prepareColumns();
  	}
  	
    public function getRowUrl1($row)
  	{
		//return $this->getUrl('*/*/*/', array('id' =>$this->getRequest()->getParam('id'),'orderid' => $row->getOrderId()));
  	}
}
