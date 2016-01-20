<?php
class MW_Affiliate_Adminhtml_AffiliatenetworkController extends Mage_Adminhtml_Controller_Action
{   
    protected $array_network = array();
    protected $array_network_table = array();
    protected $_arrayResult = array();
    
    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('affiliate/network');
        return $this;
    }   
 
    public function indexAction() 
    {
        $collection = Mage::getModel('affiliate/affiliatebinaryresidual')
            ->getCollection()
            ->addFieldToFilter('start_date', 
                    array('from'=>"2015-08-14 00:00:00", 'to'=>"2015-08-20 23:59:59"));
        
        $collection->getSelect()->join(
            array('c'=>'mw_affiliate_group_member'),
            '`main_table`.`customer_id` = `c`.`customer_id`',
            array('c.group_id')
        );

        $collection->getSelect()->join(
            array('d'=>'mw_affiliate_group'),
            '`c`.`group_id` = `d`.`group_id`',
            array('d.group_name')
        );
                
        $data = $collection->getData();        
        foreach ($data as $key => $value) {
            $percentage = 0;
            $vp = 0;
            switch ($value['group_id']) {
                case 2: $percentage = 0.10; $vp = 50; /*DISTRIBUIDOR*/ break;
                case 3: $percentage = 0.12; $vp = 50; /*BRONCE*/ break;
                case 5: $percentage = 0.15; $vp = 130;/*ORO*/ break;
                case 6: $percentage = 0.17; $vp = 130;/*ESMERALDA*/ break;
                case 8: $percentage = 0.20; $vp = 130;/*DIAMANTE*/ break;
                case 9: $percentage = 0.20; $vp = 130;/*DIAMANTE AZUL*/ break;
                case 10: $percentage = 0.20; $vp = 130; /*DIAMANTE NEGRO*/ break;
                case 11: $percentage = 0.20; $vp = 130; /*DIAMANTE CORONA*/ break;
                case 12: $percentage = 0.20; $vp = 130;/*DIAMANTE 3 CORONA*/ break;
                default: $percentage = 0; $vp = 100000000; break;
            }
                        
            $state = 0; $leg = 0; $sub = 0; $hold = 0;            
            if ($value['vp']>= $vp){
                if ($value['vg'] > 0){
                    if (($value['vpleft'] > 0) && ($value['vpright'] > 0)){
                        if ($value['vpleft'] >= $value['vpright']){
                            $sub = $value['vpright'] * $percentage;
                            $hold = $value['vpleft'] - $value['vpright'];
                            $leg = 1;
                        }else{
                            $sub = $value['vpleft'] * $percentage;
                            $hold = $value['vpright'] - $value['vpleft'];
                            $leg = 2;
                        }
                    }
                }
            }
        }
        $hold = $value['vpright'] - $value['vpleft'];
        return;
        
///        $array = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->getData();
//        foreach ($array as $key => $value) {
//            echo '<pre>';print_r($value);
//            $left = $value['right_leg'];
//            $right = $value['left_leg'];
            
//            $model = Mage::getModel('affiliate/affiliatecustomers')->load($value['customer_id']);
//            $model->setLeftLeg($left);
//            $model->setRightLeg($right);
//            $model->save();
//        }
        
        if ($this->getRequest()->getPost('email')){
            $customer = Mage::getModel('customer/customer')->loadByEmail($this->getRequest()->getPost('email'))->getData();
            
            if ($customer){
                $this->showSubAffiliateNetworkTable($customer['entity_id'],'',null,0, 0);
                Mage::register('_network_',  $this->array_network);
            }
        }
              
        $this->_initAction()->renderLayout();
    }
}

?>
