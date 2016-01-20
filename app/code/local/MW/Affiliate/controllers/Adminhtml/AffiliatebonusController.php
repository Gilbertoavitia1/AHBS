<?php
class MW_Affiliate_Adminhtml_AffiliatebonusController extends Mage_Adminhtml_Controller_Action

{  protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('affiliate/bonus');
        return $this;
    }   
 
    public function indexAction() {       
        
//        if ($this->getRequest()->getPost('email')){
//            $customer = Mage::getModel('customer/customer')->loadByEmail($this->getRequest()->getPost('email'))->getData();
//            
//            if ($customer){
//                $this->showSubAffiliateNetworkTable($customer['entity_id'],'',null,0, 0);
//                Mage::register('_network_',  $this->array_network);
//            }
//        }
              
        $this->_initAction()->renderLayout();
    }
    
    public function viewPreferredaction() { 
        $params = $this->getRequest()->getParams();
        
        $id = $params['id'];
        $start_day = date("Y/m/d H:i:s", $params['date']);
        
        $collection = Mage::getModel('affiliate/affiliatebonus')
            ->getCollection()
            ->addFieldToFilter('bonus_id', array('eq' => 2))
            ->addFieldToFilter('parent_id', array('eq' => $id))
            ->addFieldToFilter('start_day', array('eq' => $start_day));
        
        Mage::register('affiliate_bonus', $collection->getData());
        
        $this->loadLayout();
        $this->_setActiveMenu('affiliate/bonus');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

//        $this//->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatebonuspreferred_edit'))
//            ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatebonuspreferred_edit_tabs'));

        $this->renderLayout();        
    }     
    
    public function preferredcustomerAction() {
        $this->loadLayout();
        $this->_setActiveMenu('affiliate/bonus');
        $this->renderLayout();
    }
    
    public function gedionAction(){
        $month_date = strtotime(date('01/06/Y 00:00'));
            $month_date_end = strtotime(date('30/06/Y 23:59:59'));
            
            $sale = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('main_table.updated_at', array('from'=>$month_date, 'to'=>$month_date_end))
                ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
                ->addAttributeToFilter('customer_id', array('eq' => 40));    
            
            $vp = 0;
            foreach ($sale as $k => $order) {
                foreach($order->getAllVisibleItems() as $i){
                    $v = $i->getData();  

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($v['store_id'])
                        ->load($v['product_id']);

                    $puntos = $product->getData('puntos');

                    $vp = $vp + ($puntos * $v['qty_ordered']);

                }
            }
            
            
            echo  $vp;
    }
    
    public function saleAction(){        
        $dayFirst   = date("Y-m-d 00:00:00", strtotime("first day of last month"));
        $dayLast    = date("Y-m-d 23:59:59", strtotime("last day of last month"));
        
        $venta = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
            ->addAttributeToFilter('updated_at', array('from'=>$dayFirst, 'to'=>$dayLast))
            ->addAttributeToFilter('store_id', 1)
//            ->addAttributeToFilter('customer_id', array('eq' => $value['customer_id']))
//            ->setOrder('updated_at', 'ASC')
//            ->getFirstItem()
            ->getData();
        
        $filename = "mxventas" . ".xls"; 
        header("Content-Disposition: attachment; filename=\"$filename\""); 
        header("Content-Type: application/vnd.ms-excel");
        
        $flag = false;
        
        foreach($venta as $item) {
            $total = $total + $item['grand_total'];
            $subtotal = $subtotal + $item['base_subtotal'];
            $shipping = $shipping + $item['base_shipping_amount'];
            $tax = $tax + $item['base_tax_amount'];
            $info = array(
                $item['increment_id'],
                $item['created_at'],
                $item['updated_at'],
                $item['customer_firstname']. ' '.$item['customer_lastname'],
                $item['base_subtotal'],
                $item['base_tax_amount'],
                $item['base_shipping_amount'],
                $item['base_shipping_amount']                
            );
            if(!$flag) {
                echo implode("\t", array_keys($info)) . "\r\n";
                $flag = true;
            }
            array_walk($info, 'cleanData');
            echo implode("\t", array_values($info)) . "\r\n";
        }    
        
        
        return;
        $total = 0;
        $subtotal = 0;
        $shipping = 0;
        $tax = 0;
        echo '<table>';
        echo '<tr><th>Orden</th><th>Fecha Creacion</th><th>Completada</th><th>Nombre</th><th>Subtotal</th><th>Impuesto</th><th>Envio</th><th>Total</th></tr>';
        foreach ($venta as $key => $single) {
            $total = $total + $single['grand_total'];
            $subtotal = $subtotal + $single['base_subtotal'];
            $shipping = $shipping + $single['base_shipping_amount'];
            $tax = $tax + $single['base_tax_amount'];
            echo "<tr>";
            echo "<td>{$single['increment_id']}</td>";
            echo "<td>{$single['created_at']}</td>";
            echo "<td>{$single['updated_at']}</td>";
            echo "<td>{$single['customer_firstname']} {$single['customer_lastname']}</td>";
            echo "<td>{$single['base_subtotal']}</td>";
            echo "<td>{$single['base_tax_amount']}</td>";
            echo "<td>{$single['base_shipping_amount']}</td>";
            echo "<td>{$single['base_shipping_amount']}</td>";
            echo "</tr>";
//            echo '<pre>';print_r($single);
        }
        
        echo '</table>';
        echo "Shipping: {$shipping} <br>";
        echo "Sub: {$subtotal} <br>";
        echo "Total: {$total} <br>";
        echo "TAX: {$tax} <br>";
        

         
    }
    
    function cleanData(&$str) { $str = preg_replace("/\t/", "\\t", $str); $str = preg_replace("/\r?\n/", "\\n", $str); if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; }
        
    
    public function faststartAction() {
        
         Mage::helper('affiliate_faststart')->fastStartBonus(0,0);
        
    }
}

?>