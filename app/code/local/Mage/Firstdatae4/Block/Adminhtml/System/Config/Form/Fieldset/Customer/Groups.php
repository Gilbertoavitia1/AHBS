<?php 
class Mage_Firstdatae4_Block_Adminhtml_System_Config_Form_Fieldset_Customer_Groups extends Mage_Adminhtml_Block_System_Config_Form_Fieldset{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;
 
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        
        $model = Mage::getModel('firstdatae4/plans');
        $groups = $model->toOptionArray();
        
        foreach ($groups as $key=>$value) {
            $html.= $this->_getFieldHtml($element, $key, $value);
        }
        
        $html .= $this->_getFooterHtml($element);
 
        return $html;
    }
    //this creates a dummy element so you can say if your config fields are available on default and website level - you can skip this and add the scope for each element in _getFieldHtml method
    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>1));
        }
        return $this->_dummyElement;
    }
    //this sets the fields renderer. If you have a custom renderer tou can change this. 
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }
    //this is usefull in case you need to create a config field with type dropdown or multiselect. For text and texareaa you can skip it.
    protected function _getValues()
    {
        if (empty($this->_values)) {
            $model = Mage::getModel('firstdatae4/plans');
            $this->_values = $model->setPayment();
        }
        return $this->_values;
    }
    
    
    //this actually gets the html for a field
    protected function _getFieldHtml($fieldset, $id, $label)
    {
        $configData = $this->getConfigData();
        $path = 'firstdatae4/config/rp_'.$id;//this value is composed by the section name, group name and field name. The field name must not be numerical (that's why I added 'group_' in front of it)
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = (int)(string)$this->getForm()->getConfigRoot()->descend($path);
            $inherit = true;
        }
 
        $e = $this->_getDummyElement();//get the dummy element
 
        $field = $fieldset->addField($id, 'text',//this is the type of the element (can be text, textarea, select, multiselect, ...)
            array(
                'name'          => 'groups[config][fields][rp_'.$id.'][value]',//this is groups[group name][fields][field name][value]
                'label'         => $label,//this is the label of the element
                'value'         => $data,//this is the current value
                'inherit'       => $inherit,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),//sets if it can be changed on the default level
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),//sets if can be changed on website level
            ))->setRenderer($this->_getFieldRenderer());
 
        return $field->toHtml();
    }
}