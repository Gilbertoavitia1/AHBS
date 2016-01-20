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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Report Sold Products Grid Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Result_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Sub report size
     *
     * @var int
     */
    protected $_subReportSize = 0;

    /**
     * Initialize Grid settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridAffiliateReport');
    }

    /**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('affiliate/affiliatehistory_collection');
        return $this;
    }
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->unsetChild('store_switcher');
        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
    	$this->addColumn('customer_invited', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          	'align'     =>'left',
          	'index'     => 'customer_invited',
		  	'width'     => '250px',
		  	'type'      => 'text',
      		'renderer'  => 'affiliate/adminhtml_renderer_emailreport',
      	));
      	$this->addColumn('customer_id_count', array(
            'header'    =>Mage::helper('affiliate')->__('# of customers referred'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'customer_id_count',
        ));
        $this->addColumn('order_id_count', array(
            'header'    =>Mage::helper('affiliate')->__('# of orders'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'order_id_count',
        ));
        $this->addColumn('product_id_count', array(
          	'header'    => Mage::helper('affiliate')->__('# of Sales Items'),
          	'align'     =>'left',
          	'index'     => 'product_id_count',
		  	'width'     => '250px',
		  	//'type'      => 'text',
      		//'renderer'  => 'affiliate/adminhtml_renderer_productreport',
      	));
      	$this->addColumn('total_amount_sum', array(
          	'header'    => Mage::helper('affiliate')->__('Sales Total'),
          	'index'     => 'total_amount_sum',
      	    'width'		=>  '150px',
        	'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
        $this->addColumn('history_commission_sum', array(
          	'header'    => Mage::helper('affiliate')->__('Commission Total'),
          	'index'     => 'history_commission_sum',
      		'width'		=>  '150px',
      		'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
	  
      
		$this->addColumn('history_discount_sum', array(
            'header'    =>  Mage::helper('affiliate')->__('Customer Discount Total'),
        	'align'     =>  'center',
			'width'		=>  '150px',
            'index'     =>  'history_discount_sum',
			'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));

        $this->addExportType('*/*/exportSalesCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
