<?php
class MW_Affiliate_Adminhtml_AffiliatedeveloperController extends Mage_Adminhtml_Controller_Action {  
    
    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('affiliate/binary');
        return $this;
    }    
    
    public function binaryAction(){
        
    }
 
    public function testAction() {   
//        $initDate   = date("2015-11-06 00:00:00");
//        $filterFirst  = date("2015-11-06 08:00:00");
//        $endDay    = date("2015-11-12 23:59:59");
        $initDate   = date("2015-11-27 00:00:00");
        $filterFirst  = date("2015-11-27 08:00:00");
        $endDay    = date("2015-12-03 23:59:59");
       
//        Mage::helper('affiliate_binary_residual')->runBinaryResidualReport($initDate, $filterFirst, $endDay, 1233);
//        Mage::helper('affiliate_binary_residual')->calculateBonus($filterFirst, $endDay, $initDate, 1272);
//        Mage::helper('affiliate_binary_check')->runBinaryCheckReport($filterFirst);
// echo $initDate;               
       Mage::helper('affiliate_binary_constructor')->runBinaryConstructorReport($initDate, $endDay, $filterFirst);
// Mage::helper('affiliate_binary_constructor')->report($initDate, $endDay, $filterFirst); 
//        
//        
//        
//        Mage::helper('affiliate_binary_preferred')->runBinaryPreferredReport();
        
//        Mage::helper('affiliate_binary_faststart')->runBinaryFaststartReport();
//        $pay = Mage::helper('affiliate_binary_faststart')->calculateBonus(3082);
//        
//        $today = Mage::helper('affiliate_binary')->getRangeDate();
//        
//        $pay = Mage::helper('affiliate_binary_faststart')->calculateBonus($today, 3119);
//        echo '<pre>';print_R($pay);echo '</pre>';
    }
}
?>
