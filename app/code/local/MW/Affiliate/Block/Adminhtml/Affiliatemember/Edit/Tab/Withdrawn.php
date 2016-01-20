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
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Withdrawn extends Mage_Adminhtml_Block_Widget_Grid
{

 public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_member_withdrawn');
       // $this->setDefaultSort('transaction_time');
       // $this->setDefaultDir('desc');

        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Withdrawal Transaction Found'));
    }
	public function getGridUrl()
    {
    	return $this->getUrl('affiliate/adminhtml_affiliatemember/withdrawn', array('id'=>$this->getRequest()->getParam('id')));
        
    }
 protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('affiliate/affiliatewithdrawn_collection')
           				->addFieldToFilter('customer_id',$this->getRequest()->getParam('id'))
           				->setOrder('withdrawn_time', 'DESC');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {   
        $this->addColumn('withdrawn_id', array(
            'header'    =>  Mage::helper('affiliate')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'withdrawn_id',
        	'name'		=>  'withdrawn_id',
            'width'     =>  15
        ));

      	$this->addColumn('withdrawn_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Withdrawal Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'withdrawn_time',
            //'gmtoffset' => true,
            'width'     =>  100
        ));
        $this->addColumn('withdrawn_amount', array(
            'header'    =>  Mage::helper('affiliate')->__('Withdrawal Amount'),
        	'align'     =>  'left',
            'type'      =>  'price',
            'index'     =>  'withdrawn_amount',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        $this->addColumn('fee', array(
            'header'    =>  Mage::helper('affiliate')->__('Payment Processing Fee'),
        	'align'     =>  'left',
            'type'      =>  'price',
            'index'     =>  'fee',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        $this->addColumn('amount_receive', array(
            'header'    =>  Mage::helper('affiliate')->__('Net Amount'),
        	'align'     =>  'center',
            'type'      =>  'price',
            'index'     =>  'amount_receive',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
         $this->addColumn('status', array(
            'header'    =>  Mage::helper('affiliate')->__('Status'),
            'align'     =>  'center',
            'index'     =>  'status',
         	'type'      => 'options',
          	'options'   => MW_Affiliate_Model_Status::getOptionArray(),
         	'width'     =>  100
        ));
         return parent::_prepareColumns();
    }
   protected function _prepareMassaction()
    {
        $this->setMassactionIdField('withdrawn_id');
        $this->getMassactionBlock()->setFormFieldName('Affiliate_member_withdrawn');

        $statuses = MW_Affiliate_Model_Statuswithdraw::getOptionArray();

        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('affiliate/adminhtml_affiliatemember/withdrawnstatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }
    
   
}
