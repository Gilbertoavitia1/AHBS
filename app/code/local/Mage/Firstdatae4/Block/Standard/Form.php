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
 * @category   Mage
 * @package    Mage_Firstdatae4
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Firstdatae4_Block_Standard_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
		$this->setTemplate('firstdatae4/standard/form.phtml');
		
		// We need to ask only the CC type from the customer - rest of the information will be collected
		// by FirstdataE4 when we redirect to their servers. Also, the value for each card type is different
		// from the normal values in Magento. Hence, we need to specifically pass them to our custom template
		$this->setData('cctypes', Mage::getModel('Mage_Firstdatae4_Model_Standard')->getConfigData('cctypes'));
        parent::_construct();
    }
}