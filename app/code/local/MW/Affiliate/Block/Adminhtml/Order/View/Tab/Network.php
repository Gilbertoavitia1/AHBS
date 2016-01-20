<?php
class MW_Affiliate_Block_Adminhtml_Order_View_Tab_Network extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{    
    public function __construct()
    {
        parent::_construct();        
        $this->setTemplate('mw_affiliate/sales/order/view/tab/network.phtml');
    }

    public function getTabLabel() {
        return $this->__('Binary Information');
    }

    public function getTabTitle() {
        return $this->__('Binary Information');
    }

    public function getOrder(){
        return Mage::registry('current_order');
    }
    public function canShowTab() {
//        $order = $this->getOrder();
//        if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE){
           return true;
//        }
//        else{
//            return false;
//        }
    }

    public function isHidden() {
        return false;
    }
} 
?>