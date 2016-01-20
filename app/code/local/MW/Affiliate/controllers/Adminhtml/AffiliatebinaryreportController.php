<?php
class MW_Affiliate_Adminhtml_AffiliatebinaryreportController extends Mage_Adminhtml_Controller_Action

{  protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('affiliate/binary');
        return $this;
    }
    
    public function residualAction(){      
        $this->loadLayout();
        $this->renderLayout();
    }
        
    public function checkAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function constructorAction(){
        $today = Mage::helper('affiliate_binary')->getRangeDate();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "DELETE FROM mw_affiliate_binary_constructor WHERE start_date = '{$today[2]}'";
        $write->query($sql);           
        Mage::helper('affiliate_binary_constructor')->runBinaryConstructorReport($today[0], $today[1], $today[2]);
        
         $this->_getSession()->addSuccess($this->__('The builder bonus was updated'));
        
        $this->_redirect('*/adminhtml_affiliatebinary/constructor'); 
    }
    
    public function preferredAction(){
        $filterDay   = date("Y-m-d 08:00:00", strtotime("first day of last month"));
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "DELETE FROM mw_affiliate_binary_preferred WHERE start_date = '{$filterDay}'";
        $write->query($sql);  
        Mage::helper('affiliate_binary_preferred')->runBinaryPreferredReport();
        $this->_getSession()->addSuccess($this->__('The Preferred bonus was updated'));  
        $this->_redirect('*/adminhtml_affiliatebinary/preferred'); 
    }
    
    public function faststartAction(){
        $this->loadLayout();
        $this->renderLayout();
    }    
    
    public function massRunReportResidualAction()
    {  
        $today = Mage::helper('affiliate_binary')->getRangeDate();
        $customerIds = $this->getRequest()->getParam('affiliatebinaryreport_residual');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
                foreach ($customerIds as $customerId) {
                    Mage::helper('affiliate_binary_residual')->runBinaryResidualReport($today[0], $today[2], $today[1], $customerId);
                    Mage::helper('affiliate_binary_residual')->calculateBonus($today[2], $today[1], $today[0], $customerId);
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        
        $this->_redirect('*/*/residual');
    }
    
    public function massRunReportCheckAction()
    {  
        $today = Mage::helper('affiliate_binary')->getRangeDate();
        $customerIds = $this->getRequest()->getParam('affiliatebinaryreport_check');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
                foreach ($customerIds as $customerId) {
                    Mage::helper('affiliate_binary_check')->runBinaryCheckReport($today[2], $customerId);
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        
        $this->_redirect('*/*/check');
    }
    
    public function massDeleteReportCheckAction()
    {  
        $today = Mage::helper('affiliate_binary')->getRangeDate();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "DELETE FROM mw_affiliate_binary_check WHERE start_date = '{$today[2]}'";
        $write->query($sql);         
        $this->_redirect('*/*/check');        
    }
    
    public function massRunReportFaststartAction()
    {  
        $today = Mage::helper('affiliate_binary')->getRangeDate();
        $sale_order = $this->getRequest()->getParam('affiliatebinaryreport_faststart');
        if(!is_array($sale_order)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select order(s)'));
        } else {
            try {
                foreach ($sale_order as $order) {
                    $pay = Mage::helper('affiliate_binary_faststart')->calculateBonus($today, $order);                    
                    $action = Mage::getModel('affiliate/affiliatebinaryfaststart')->getCollection()
                        ->addFieldToFilter('order_id',array('eq' => $order))
                        ->getFirstItem()    
                        ->getData();
                    
                    if (sizeof($action) > 0){
                        $model = Mage::getModel('affiliate/affiliatebinaryfaststart')->load($action['id']);
                        $model->addData($pay)->save();
                        
                        
                    }else{
                        $model = Mage::getModel('affiliate/affiliatebinaryfaststart');
                        $model->addData($pay);
                        $model->save();
                    }                    
                }
                
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($sale_order))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        
        $this->_redirect('*/*/faststart');
    }
}
?>