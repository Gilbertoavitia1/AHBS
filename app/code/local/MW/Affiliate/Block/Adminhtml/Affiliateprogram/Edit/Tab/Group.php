<?php

class MW_Affiliate_Block_Adminhtml_Affiliateprogram_Edit_Tab_Group extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliate_program_group');
      $this->setDefaultSort('group_id');
      //$this->setDefaultDir('ASC');
   	  $this->setUseAjax(true);
      $collection = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
        				->addFieldToFilter('program_id',$this->getRequest()->getParam('id'));
	  if(sizeof($collection) > 0){
	        	$this->setDefaultFilter(array('in_program_group'=>1));
	  	}
  }
  public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/groupGrid', array('_current'=>true));
    }
 	// trong truong hop search 
    // voi yes, any.....
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_program_group') {
            $groupIds = $this->_getSelectedGroups();
            if (empty($groupIds)) {
                $groupIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('group_id', array('in'=>$groupIds));
            } else {
                if($groupIds) {
                    $this->getCollection()->addFieldToFilter('group_id', array('nin'=>$groupIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
  protected function _prepareCollection()
  {   
      $collection = Mage::getModel('affiliate/affiliategroup')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  protected function _prepareColumns()
  {   
  	 $this->addColumn('in_program_group', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->_getSelectedGroups(),
                'align'             => 'center',
                'index'             => 'group_id'
            ));
      $this->addColumn('group_id', array(
          'header'    => Mage::helper('affiliate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'group_id',
      ));

     $this->addColumn('group_name', array(
          'header'    => Mage::helper('affiliate')->__('Add Group to Affiliate Program (Reset Filter to see ALL Groups)'),
          'align'     =>'left',
          'index'     => 'group_name',
      ));
      $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true,
        ));
	  
      return parent::_prepareColumns();
  }
 // tao ra mang cac program ma khach hang da tham gia
	protected function _getSelectedGroups()
    {
        $groups = array_keys($this->getSelectedAddGroups());
        return $groups;
    }

    public function getSelectedAddGroups()
    {
        
    	$collection = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
        				->addFieldToFilter('program_id',$this->getRequest()->getParam('id'));
        $groups = array();
        
        foreach ($collection as $group) {
            $groups[$group->getGroupId()] = $group->getGroupId();
        }
        return $groups;
    }

}