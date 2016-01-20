<?php
class Ebcomm_PaypalMx_Block_Adminhtml_System_Config_Fieldset_Payment
        extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Add custom css class
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button '
            . ($this->_isPaymentEnabled($element) ? ' enabled' : '');
    }

    /**
     * Check whether current payment method is enabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isPaymentEnabled($element)
    {
        $groupConfig = $this->getGroup($element)->asArray();
        $activityPath = isset($groupConfig['activity_path']) ? $groupConfig['activity_path'] : '';

        if (empty($activityPath)) {
            return false;
        }

        $isPaymentEnabled = (string)Mage::getSingleton('adminhtml/config_data')->getConfigDataValue($activityPath);

        return (bool)$isPaymentEnabled;
    }

    /**
     * Return header title part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading" style="display:block; clear:both; content:"."; font-size:0; line-height:0; height:0; overflow:hidden;" ><div class="heading" style="padding-left:56px; background:url('.$this->getSkinUrl('images/paypalmx/logo-paypal.png').') no-repeat 0 2px;"><strong>' . $element->getLegend();

        $groupConfig = $this->getGroup($element)->asArray();
        if (!empty($groupConfig['learn_more_link'])) {
            $html .= '<a class="link-more" href="' . $groupConfig['learn_more_link'] . '" target="_blank">'
                . $this->__('Aprende más') . '</a>';
        }
        if (!empty($groupConfig['demo_link'])) {
            $html .= '<a class="link-demo" href="' . $groupConfig['demo_link'] . '" target="_blank">'
                . $this->__('Vér Demo') . '</a>';
        }
        $html .= '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }
        $html .= '</div>';

        $html .= '<div class="button-containermx" style="float:right; padding:0 0 0 0; ">
        		<img src="'.$this->getSkinUrl('images/paypalmx/accpmark_mensualidades_SP.PNG').'" width="160px" height="30px">
        		<img src="'.$this->getSkinUrl('images/paypalmx/accpmark_tarjdeb_SP.PNG').'" width="160px" height="30px" style=" padding: 0 0 0 5px;">
        		<img src="'.$this->getSkinUrl('images/paypalmx/accpmark_visa_mc_SP.PNG').'" width="75px" height="30px" style=" margin: 0 10px 0 5px; ">
        				<button type="button"'
            . ($this->_isPaymentEnabled($element) ? '' : ' disabled="disabled"') . ' class="button'
            . (empty($groupConfig['paypal_ec_separate']) ? '' : ' paypal-ec-separate')
            . ($this->_isPaymentEnabled($element) ? '' : ' disabled') . '" id="' . $element->getHtmlId()
            . '-head" onclick="paypalToggleSolution.call(this, \'' . $element->getHtmlId() . '\', \''
            . $this->getUrl('*/*/state') . '\'); return false;"><span class="state-closed">'
            . $this->__('Configurar') . '</span><span class="state-opened">'
            . $this->__('Cerrar') . '</span></button></div></div>';

        return $html;
    }

    /**
     * Return header comment part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * Get collapsed state on-load
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _getCollapseState($element)
    {
        return false;
    }
}
