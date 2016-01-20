<?php
/**
 * Custom renderer for PayPal API credentials wizard popup
 */
class Ebcomm_PaypalMx_Block_Adminhtml_System_Config_ApiWizard extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Set template to itself
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('paypalmx/system/config/api_wizard.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('paypalmx')->__($originalData['button_label']),
            'button_url'   => $originalData['button_url'],
            'html_id' => $element->getHtmlId(),
            'sandbox_button_label' => Mage::helper('paypalmx')->__($originalData['sandbox_button_label']),
            'sandbox_button_url'   => $originalData['sandbox_button_url'],
            'sandbox_html_id' => 'sandbox_' . $element->getHtmlId(),
        ));
        return $this->_toHtml();
    }
}
