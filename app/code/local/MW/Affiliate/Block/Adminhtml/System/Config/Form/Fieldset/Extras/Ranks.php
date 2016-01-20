<?php 
class MW_Affiliate_Block_Adminhtml_System_Config_Form_Fieldset_Extras_Ranks extends Mage_Adminhtml_Block_System_Config_Form_Fieldset{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;
 
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        
        $model = Mage::getModel('affiliate/ranks');
        $groups = $model->toOptionArray();
        
        foreach ($groups as $key=>$value) {
            $html.= $this->_getFieldHtml($element, $key, $value);
        }       
        
        $html .= $this->_getFooterHtml($element);
 
        return $html;
    }
    
    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>1));
        }
        return $this->_dummyElement;
    }    
    
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }
    
    protected function _getValues()
    {
        if (empty($this->_values)) {
            $model = Mage::getModel('affiliate/ranks');
            $this->_values = $model->getRanks();
        }
        return $this->_values;
    }
    
    protected function _getFieldHtml($fieldset, $id, $label)
    {
        $configData = $this->getConfigData();
        $path = 'extras/ranks/'.$id;//this value is composed by the section name, group name and field name. The field name must not be numerical (that's why I added 'group_' in front of it)
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
                'name'          => 'groups[ranks][fields]['.$id.'][value]',//this is groups[group name][fields][field name][value]
                'label'         => $label,//this is the label of the element
                'value'         => $data,//this is the current value
//                'values'        => $this->_getValues(),//this is necessary if the type is select or multiselect
                'inherit'       => $inherit,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),//sets if it can be changed on the default level
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),//sets if can be changed on website level
            ))->setRenderer($this->_getFieldRenderer());
 
        return $field->toHtml();
    }
}