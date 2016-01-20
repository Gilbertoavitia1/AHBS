<?php

class Ebcomm_PaypalMx_Model_System_Config_Backend_Cert extends Mage_Core_Model_Config_Data
{
    /**
     * Process additional data before save config
     *
     * @return Mage_Paypal_Model_System_Config_Backend_Cert
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            Mage::getModel('paypalmx/cert')->loadByWebsite($this->getScopeId())->delete();
        }

        if (!isset($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])) {
            return $this;
        }
        $tmpPath = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
        if ($tmpPath && file_exists($tmpPath)) {
            if (!filesize($tmpPath)) {
                Mage::throwException(Mage::helper('paypalmx')->__('El certificado paypal esta vacio..'));
            }
            $this->setValue($_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']);
            $content = Mage::helper('core')->encrypt(file_get_contents($tmpPath));
            Mage::getModel('paypalmx/cert')->loadByWebsite($this->getScopeId())
                ->setContent($content)
                ->save();
        }
        return $this;
    }

    /**
     * Process object after delete data
     *
     * @return Mage_Paypal_Model_System_Config_Backend_Cert
     */
    protected function _afterDelete()
    {
        Mage::getModel('paypalmx/cert')->loadByWebsite($this->getScopeId())->delete();
        return $this;
    }
}
