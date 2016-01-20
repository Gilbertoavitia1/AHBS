<?php
class MW_Affiliate_Adminhtml_AffiliateindexController extends Mage_Adminhtml_Controller_Action
{
    public function savenetworkAction() {
        $params = $this->getRequest()->getParams();
        $id = $params['order_id'];
        $viewUrl = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view',array('order_id' => $id));
        if ($this->getRequest()->getPost('affiliate_network')){            
            $order = Mage::getModel("sales/order")->load($id);
            $order->setAffiliateNetwork($this->getRequest()->getPost('affiliate_network'));
            $order->setAffiliateSaleType($this->getRequest()->getPost('affiliate_sale_type'));
            $order->save();
            
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql = "UPDATE sales_flat_order_grid SET affiliate_network = {$this->getRequest()->getPost('affiliate_network')}, affiliate_sale_type = {$this->getRequest()->getPost('affiliate_sale_type')} WHERE entity_id = '{$id}'";

            $write->query($sql);        
            
            if (($order->getStatus() == 'complete') && (($order->getAffiliateSaleType() == 1) || ($order->getAffiliateSaleType() == 2))){            
                Mage::helper('affiliate_binary')->setExpiredAutomatic($order);
            }
        
            $this->_getSession()->addSuccess($this->__('Orden Guardada'));   
        }
        
        Mage::app()->getResponse()->setRedirect($viewUrl);
    }
   
}


