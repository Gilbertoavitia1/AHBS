<?php
class Inchoo_Heared4us_Model_Observer
{
    const ORDER_ATTRIBUTE_FHC_ID = 'heared4us';
    
    public function hookToOrderSaveEvent()
    {        
        $order = new Mage_Sales_Model_Order();
        $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order->loadByIncrementId($incrementId);

        //Fetch the data from select box and throw it here
        $_heared4us_data = null;
        $_heared4us_data = Mage::getSingleton('core/session')->getInchooHeared4us();

        //Save fhc id to order obcject
        $order->setData(self::ORDER_ATTRIBUTE_FHC_ID, $_heared4us_data);
        $order->setData("affiliate_sale_type", $_heared4us_data);
        $order->save();


        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "UPDATE sales_flat_order_grid SET affiliate_sale_type = {$_heared4us_data} WHERE entity_id = '{$order->getEntityId()}'";

        $write->query($sql);
        
        
        if ($_heared4us_data != 3){
            $entity = $order->getEntityId();
            $customer_id = $order->getCustomerId();
            $expired = Mage::getModel('affiliate/affiliateexpired')->load($customer_id)->getData();
            $historic = '[{"order":"'.$entity.'"}]';
            $today = date("Y-m-d 23:59:59");
            $expired_date = new DateTime($today);
            $interval = new DateInterval('P1M');
            $expired_date->add($interval);
            $final = $expired_date->format('Y-m-d h:i:s');
            
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            if ($expired){
                $sql = "UPDATE mw_affiliate_expired SET historic='{$historic}',  expired_package = '{$final}' WHERE customer_id = '{$customer_id}'";                
            }else{
                $sql = "INSERT INTO mw_affiliate_expired VALUES({$customer_id}, '{$final}', NULL, '{$historic}')";
            }
            
            $write->query($sql);
        }
    }
}