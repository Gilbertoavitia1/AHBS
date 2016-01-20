<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebanner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
  	{
      	$form = new Varien_Data_Form();
      	$this->setForm($form);
      	$fieldset = $form->addFieldset('affiliate_banner_form', array('legend'=>Mage::helper('affiliate')->__('Banner information')));
      	$fieldset->addField('status', 'select', array(
        	'label'     => Mage::helper('affiliate')->__('Status'),
          	'name'      => 'status',
          	'values'    => array(
              	array(
                	'value'     => 1,
                  	'label'     => Mage::helper('affiliate')->__('Enabled'),
              	),

              	array(
                	'value'     => 2,
                  	'label'     => Mage::helper('affiliate')->__('Disabled'),
            	),
        	),
      	));
      	$fieldset->addField('title_banner', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Title'),
          	'class'     => 'required-entry',
          	'required'  => true,
          	'name'      => 'title_banner',
      	));
      	$fieldset->addField('group_id', 'multiselect', array(
        	'label'     => Mage::helper('affiliate')->__('Group Name'),
          	'name'      => 'group_id[]',
      	  	'required'  => true,
          	'values'    => $this->_getGroupArray(),
	    ));
  	  	//Store View
  	  	if(!Mage::app()->isSingleStoreMode()) {
        	$fieldset->addField('store_view', 'multiselect', array(
            	'name'      => 'store_view[]',
                'label'     => Mage::helper('affiliate')->__('Store View'),
                'title'     => Mage::helper('affiliate')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
  	    } 
        else 
        {
            $fieldset->addField('store_view', 'hidden', array(
                'name'      => 'store_view[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
        }
      	$fieldset->addField('link_banner', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Banner Link'),
          	'class'     => 'required-entry',
          	'required'  => true,
          	'name'      => 'link_banner',
      	));

      	$fieldset->addField('width', 'text', array(
          	'label'     => Mage::helper('affiliate')->__('Width'),
          	'class'     => 'required-entry validate-digits',
          	'required'  => true,
      	 	'note' 		=> Mage::helper('affiliate')->__('Unit: Pixel (37.8 pixels = 1 cm)'),
          	'name'      => 'width',
      	));
      	$fieldset->addField('height', 'text', array(
          	'label'     => Mage::helper('affiliate')->__('Height'),
          	'class'     => 'required-entry validate-digits',
          	'required'  => true,
          	'note' 		=> Mage::helper('affiliate')->__('Unit: Pixel (37.8 pixels = 1 cm)'),
          	'name'      => 'height',
      	));
      	$fieldset->addField('image_name', 'image', array(
        	'label'     => Mage::helper('affiliate')->__('Upload Image'),
          	'required'  => true,
          	'name'      => 'image_name',
	  	));
     
      	if (Mage::getSingleton('adminhtml/session')->getInvitationData())
      	{   
          	$form->setValues(Mage::getSingleton('adminhtml/session')->getInvitationData());
        	Mage::getSingleton('adminhtml/session')->setTestData(null);
 	    } 
      	else if(Mage::registry('affiliate_data_banner')) 
      	{
        	$form->setValues(Mage::registry('affiliate_data_banner')->getData());
          	$image_name=Mage::registry('affiliate_data_banner')->getData('image_name');
          	if(isset($image_name)&& $image_name !='')
          	{  
          		$image =$image_name;
          		$form->getElement('image_name')->setValue($image);
          	}
      	}
    	return parent::_prepareForm();
  	}
  
	private function _getGroupArray()
    {
    	$arr = array();
    	$groups = Mage::getModel('affiliate/affiliategroup')->getCollection();
		foreach ($groups as $group) 
		{   
			$group_id = $group->getGroupId();
			$group_name = $group ->getGroupName();
			$arr[] = array(
                        'label' => $group ->getGroupName(),
                        'value' => $group->getGroupId()
                    );
		}
		return $arr;
    }
}