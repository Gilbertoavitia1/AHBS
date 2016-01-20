<?php

class Mage_Firstdatae4_Block_Adminhtml_Recurring_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);

          $data = Mage::registry('recurring_data')->getData();
    
      $storelocatorobj = Mage::getModel('storelocator/storelocator')->load($this->getRequest()->getParam('id'));
        
      $fieldset = $form->addFieldset('storelocator_form', array('legend'=>Mage::helper('storelocator')->__('Store Information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('storelocator')->__('Store Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));
      
       if($storelocatorobj && $storelocatorobj->getId() && $storelocatorobj['storeimage']){
            $imgPath = $this->helper('storelocator')->getImagepath().$storelocatorobj['storeimage'];   
            $fieldset->addField('image', 'label', array(
                'label' => Mage::helper('storelocator')->__('Store Image'),
                'name'  =>'image',
                'value'     => $imgPath,
                'after_element_html' => '<img src="'.$imgPath .'" alt=" '. $imgPath .'" height="120" width="120" />',
            ));   
        } 
       if(isset($data['storeimage']) && $data['storeimage']){
                $data['storeimage'] = 'storelocator/images/' . $data['storeimage'];
       }  
       if($storelocatorobj && $storelocatorobj->getId() && $storelocatorobj['storeimage']){
           $fieldset->addField('storeimage', 'image', array(
            'label'     => Mage::helper('storelocator')->__('Change Store Image'),     
            'note'      => 'Store Image shown on map',
            'name'      => 'storeimage',		 
        ));
        }else{
            $fieldset->addField('storeimage', 'image', array(
            'label'     => Mage::helper('storelocator')->__('Choose Store Image'),     
            'note'      => 'Store Image shown on map',
            'name'      => 'storeimage',		 
        ));
        }    
        
         $fieldset->addField('longitude', 'text', array(
               'label'     => Mage::helper('storelocator')->__('Store Longitude'),                
               'name'      => 'longitude',
               'required'  => true,
               'class'     => 'required-entry',
        ));
        
        $fieldset->addField('latitude', 'text', array(
                'label'     => Mage::helper('storelocator')->__('Store Latitude'),                
                'name'      => 'latitude',
                'required'  => true,
                'class'     => 'required-entry',
        ));
        
        $fieldset->addField('zoom_level', 'text', array(
                'label'     => Mage::helper('storelocator')->__('Zoom Level'),                
                'name'      => 'zoom_level',
        ));
        
      $fieldset->addField('address', 'text', array(
            'label' => Mage::helper('storelocator')->__('Address'),
            'name' => 'address',
            'required' => true,
            'class' => 'required-entry',
        ));
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('storelocator')->__('City'),
            'name' => 'city',
            'required' => true,
            'class' => 'required-entry',
        ));

        
        $fieldset->addField('country', 'select', array(
            'label' => Mage::helper('storelocator')->__('Country'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'country',
            'values' => Mage::helper('storelocator')->getCountryOptions(),
         ));
        
         $fieldset->addField('stateEL', 'note', array(
            'label' => Mage::helper('storelocator')->__('State / Province'),
            'name' => 'stateEL',
            'text' => $this->getLayout()->createBlock('storelocator/adminhtml_region')->setTemplate('storelocator/region.phtml')->toHtml(),
        ));
         
        $fieldset->addField('zipcode', 'text', array(
            'label' => Mage::helper('storelocator')->__('Zip Code'),
            'name' => 'zipcode', 
            'required'  => true,
            'class'     => 'required-entry',
        ));
        
        $fieldset->addField('phone', 'text', array(
            'label' => Mage::helper('storelocator')->__('Telephone'),
            'name' => 'phone',                        
        ));
        
//        $fieldset->addField('fax', 'text', array(
//            'label' => Mage::helper('storelocator')->__('Fax'),
//            'name' => 'fax',                        
//        ));
        
        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('storelocator')->__('Email'),
            'name' => 'email',                        
        ));
        
        $fieldset->addField('web_url', 'text', array(
            'label' => Mage::helper('storelocator')->__('Website Url'),
            'name' => 'web_url',
            'required' => false,
        ));
        
        $fieldset->addField('hours1', 'text', array(
            'label' => Mage::helper('storelocator')->__('Time Duration 1'),
            'name' => 'hours1',                        
        ));
        
        $fieldset->addField('hours2', 'text', array(
            'label' => Mage::helper('storelocator')->__('Time Duration 2'),
            'name' => 'hours2',                        
        ));
        
        $fieldset->addField('hours3', 'text', array(
            'label' => Mage::helper('storelocator')->__('Time Duration 3'),
            'name' => 'hours3',                        
        ));

      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('storelocator')->__('Description'),
          'title'     => Mage::helper('storelocator')->__('Description'),
          'style'     => 'width:500px; height:250px;',
          'wysiwyg'   => true,
          'required'  => false,
      ));
		
//        $fieldset->addField('sortorder', 'text', array(
//            'label' => Mage::helper('storelocator')->__('Sort Order'),
//            'name' => 'sortorder',                        
//        ));
        
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('storelocator')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('storelocator')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('storelocator')->__('Disabled'),
              ),
          ),
      ));
     
     
      if ( Mage::getSingleton('adminhtml/session')->getStorelocatorData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getStorelocatorData());
          Mage::getSingleton('adminhtml/session')->setStorelocatorData(null);
      } elseif ( Mage::registry('storelocator_data') ) {
          $form->setValues(Mage::registry('storelocator_data')->getData());
      }
      return parent::_prepareForm();
  }
}