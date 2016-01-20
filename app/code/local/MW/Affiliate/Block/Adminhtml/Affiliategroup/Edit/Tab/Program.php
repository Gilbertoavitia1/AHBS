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
class MW_Affiliate_Block_Adminhtml_Affiliategroup_Edit_Tab_Program extends Mage_Adminhtml_Block_Widget_Grid
{

 	public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_group_program');
        $this->setDefaultSort('program_id');
        $this->setUseAjax(true);
        $collection = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
        				->addFieldToFilter('group_id',$this->getRequest()->getParam('id'));
	    if(sizeof($collection) > 0){
	        	$this->setDefaultFilter(array('in_group_program'=>1));
	        }
    }
 	public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/programGrid', array('_current'=>true));
    }
    // trong truong hop search 
    // voi yes, any.....
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_group_program') {
            $programIds = $this->_getSelectedPrograms();
            if (empty($programIds)) {
                $programIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('program_id', array('in'=>$programIds));
            } else {
                if($programIds) {
                    $this->getCollection()->addFieldToFilter('program_id', array('nin'=>$programIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
	// loc ra trong cac program ma khach hang da tham gia
	protected function _prepareCollection()
    {   
    	$programIds = $this->_getSelectedPrograms();
		$collection = Mage::getModel('affiliate/affiliateprogram')->getCollection();
	  					//->addFieldToFilter('program_id', array('in' => $programIds));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

  protected function _prepareColumns()
  {   
  	 $this->addColumn('in_group_program', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->_getSelectedPrograms(),
                'align'             => 'center',
                'index'             => 'program_id'
            ));
      $this->addColumn('program_id', array(
          'header'    => Mage::helper('affiliate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'program_id',
      ));

      $this->addColumn('program_name', array(
          'header'    => Mage::helper('affiliate')->__('Program Name (Reset Filter to see All Programs)'),
          'align'     =>'left',
          'index'     => 'program_name',
      ));
      $this->addColumn('start_date', array(
			'header'    => Mage::helper('affiliate')->__('Start Date'),
			'width'     => '150px',
			'index'     => 'start_date',
      ));
      $this->addColumn('end_date', array(
			'header'    => Mage::helper('affiliate')->__('End Date'),
			'width'     => '150px',
			'index'     => 'end_date',
      ));
	  $this->addColumn('total_members', array(
          'header'    => Mage::helper('affiliate')->__('Total Members'),
          'align'     =>'left',
	  	  'type'      => 'number',
          'index'     => 'total_members',
      ));
      $this->addColumn('total_commission', array(
          'header'    => Mage::helper('affiliate')->__('Total Commission'),
          'align'     =>'left',
      	  'type'      => 'price',
          'index'     => 'total_commission',
      	  'currency_code' => Mage::app()->getBaseCurrencyCode(),
      ));
      $this->addColumn('status', array(
          'header'    => Mage::helper('affiliate')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
      $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            //'edit_only'         => !$this->_getProduct()->getId()
            'edit_only'         => true,
        ));
      return parent::_prepareColumns();
  }
  // tao ra mang cac program ma khach hang da tham gia
	protected function _getSelectedPrograms()
    {
        $programs = array_keys($this->getSelectedAddPrograms());
        return $programs;
    }

    public function getSelectedAddPrograms()
    {
        
    	$collection = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
        				->addFieldToFilter('group_id',$this->getRequest()->getParam('id'));
        $programs = array();
        
        foreach ($collection as $program) {
            $programs[$program->getProgramId()] = $program->getProgramId();
        }
        return $programs;
    }
	
   
}
