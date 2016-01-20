<?php
/**
 * Customer Credit form block (Step2)
 *
 * @category   MW
 * @package    MW_Credit
 * @author Mage World <support@mage-world.com>
 */
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Credit extends Mage_Adminhtml_Block_Widget_Form
{
	
//    public function __construct()
//    {
//        parent::__construct();
//        $this->setTemplate('mw_credit/customer/tab/credit.phtml');
//        
//    }
    
    /**
     * Colection Order is refund
     * 
     * @return array
     */
	private function _getCreditmemo()
    {   
    	$customer_id = $this->getRequest()->getParam('id'); 
    	$arr = array();
    	$collection = Mage::getResourceModel('sales/order_collection')
			->addAttributeToSelect('*')						
			->addAttributeToFilter('customer_id', $customer_id);
		foreach($collection as $order){ 
			if($order['status'] == Mage_Sales_Model_Order::STATE_CLOSED)
				$arr[$order['increment_id']] = $order['increment_id'];
		} 
		return $arr;
    }

   protected function _prepareForm()
   {   
    	$default_config=0;
    	
        $form_credit = new Varien_Data_Form();
      	$this->setForm($form_credit);
      	$fieldset = $form_credit->addFieldset('affiliate_form_credit', array('legend'=>Mage::helper('affiliate')->__('Credit Information')));
        $customer_id = $this->getRequest()->getParam('id');
      	$credit_customer = Mage::getModel('credit/creditcustomer')->load($customer_id);
        $credit = $credit_customer->getCredit();

        $fieldset->addField('credit', 'label', array(
           	'label' => Mage::helper('affiliate')->__('Current balance'),
           	'name'  => 'credit',
        	'value' => Mage::helper('core')->currency($credit,true,false)
       	));
       	
       	$fieldset->addField('amount_credit', 'text', array(
           	'label' => Mage::helper('affiliate')->__('Manual Adjustment'),
           	'name'  => 'amount_credit',
       		'note' => Mage::helper('catalog')->__('Amount of Credit which you want to add or subtract to Affiliate Account. Ex. 50, -50.'),
       		'class' => 'validate-number',
       	));
       	
       	$fieldset->addField('payout_credit', 'text', array(
           	'label' => Mage::helper('affiliate')->__('Manual Payout'),
           	'name'  => 'payout_credit',
       		'note' => Mage::helper('affiliate')->__('Amount of Credit which you want to pay. Ex. 50'),
       		'class' => 'validate-number',
       	));
       	
       	$fieldset->addField('comment', 'textarea', array(
           	'label' => Mage::helper('affiliate')->__('Accompanying Comment'),
           	'name'  => 'comment',
       	));
        
        return parent::_prepareForm();
    }

}
