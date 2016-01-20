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
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Referral_Grid extends Mage_Adminhtml_Block_Report_Grid
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
        $this->setId('gridAffiliateReportReferral');
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
            ->initReport('affiliate/affiliateinvitation_collection');
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
    	$this->addColumn('customer_id', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          	'align'     =>'left',
          	'index'     => 'customer_id',
		  	'width'     => '250px',
		  	'type'      => 'text',
      		'renderer'  => 'affiliate/adminhtml_renderer_emailreferral',
      	));
        $this->addColumn('count_click_link_sum', array(
            'header'    =>Mage::helper('affiliate')->__('# of Clicks'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'count_click_link_sum',
        ));
        $this->addColumn('count_register_sum', array(
            'header'    =>Mage::helper('affiliate')->__('# of New Accounts'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'count_register_sum',
        ));
        $this->addColumn('count_purchase_sum', array(
            'header'    =>Mage::helper('affiliate')->__('# of Purchases'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'count_purchase_sum',
        ));
       /* $this->addColumn('status', array(
            'header'    =>Mage::helper('affiliate')->__('Status'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'status',
        	'renderer'  => 'affiliate/adminhtml_renderer_statusreferral',
        ));*/
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('affiliate')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }
        $this->addExportType('*/*/exportReferralCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportReferralExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
