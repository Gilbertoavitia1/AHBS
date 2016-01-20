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
class MW_Affiliate_Block_Adminhtml_Affiliateprogram_Edit_Tab_Transaction extends Mage_Adminhtml_Block_Widget_Grid
{

 public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_program_transaction');
       // $this->setDefaultSort('transaction_time');
       // $this->setDefaultDir('desc');

        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Affiliate Transaction Found'));
    }
	public function getGridUrl()
    {
    return $this->getUrl('affiliate/adminhtml_affiliateprogram/transaction', array('id'=>$this->getRequest()->getParam('id')));
        
    }
	 protected function _prepareCollection()
    {   
    	//$customer = Mage::getModel('customer/customer')->getCollection();
    	$resource = Mage::getModel('core/resource');
  	  	$customer_table = $resource->getTableName('customer/entity');
        $collection = Mage::getResourceModel('affiliate/affiliatehistory_collection')
           				->addFieldToFilter('program_id',$this->getRequest()->getParam('id'))
						->addFieldToFilter('program_id',array('neq'=>0))
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');
		
		$collection->getSelect()->join(
      							array('customer_entity'=>$customer_table),'main_table.customer_invited = customer_entity.entity_id',array('email'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	private function _getNameProduct()
    {
    	$arr = array();
    	$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name');
		foreach($collection as $item){
			$arr[$item->getId()] = $item->getName();
		}
        return $arr;
    }
//	private function _getEmailCustomerInvited()
//    {
//    	$arr = array();
//    	$collection = Mage::getModel('customer/customer')->getCollection();
//		foreach($collection as $item){
//			$arr[$item->getId()] = $item->getEmail();
//		}
//        return $arr;
//    }
    protected function _prepareColumns()
    {   
    	//$this->setTemplate('mw_credit/gridaffiliateinfomation.phtml');
        $this->addColumn('history_id', array(
            'header'    =>  Mage::helper('affiliate')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'history_id',
            'width'     =>  10
        ));
        
      	$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));
   	   	$this->addColumn('product_id', array(
     		'header'    => Mage::helper('affiliate')->__('Product Name'),
    	    'align'     =>'left',
   	       	'index'     => 'product_id',
			'type'      => 'text',
            'renderer'  => 'affiliate/adminhtml_renderer_productname',
   	   	));
   	   	
//		$this->addColumn('customer_invited', array(
//          	'header'    => Mage::helper('affiliate')->__('Affiliate Email'),
//          	'align'     =>'left',
//          	'index'     => 'customer_invited',
//		  	'width'     => '250px',
//		  	'type'      => 'options',
//          	'options'   => $this->_getEmailCustomerInvited(),
//      	));
          $this->addColumn('email', array(
          'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          'align'     =>'left',
          'index'     => 'email',
	  	  'renderer'  => 'affiliate/adminhtml_renderer_emailaffiliatemember',
       ));
        $this->addColumn('order_id', array(
            'header'    =>  Mage::helper('affiliate')->__('Order Number'),
            'align'     =>  'left',
        	'width'		=>  100,
            'index'     =>  'order_id',
        	'type'      => 'text',
            'renderer'  => 'affiliate/adminhtml_renderer_orderid',
        ));
      	
      	$this->addColumn('total_amount', array(
          	'header'    => Mage::helper('affiliate')->__('Product Price'),
          	'index'     => 'total_amount',
        	'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
      	
      	$this->addColumn('history_commission', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Commission'),
          	'index'     => 'history_commission',
      		'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
	  
      
		$this->addColumn('history_discount', array(
            'header'    =>  Mage::helper('affiliate')->__('Customer Discount'),
        	'align'     =>  'center',
            'index'     =>  'history_discount',
			'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        
        $this->addColumn('status', array(
          	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     =>'center',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('affiliate/status')->getOptionArray(),
      	));
        
         return parent::_prepareColumns();
    }
	
   
}
