<?php
class MW_Affiliate_Block_Affiliate_Network extends Mage_Core_Block_Template
{
    protected $array_network = array();
    protected $array_network_table = array();
    protected $sponsored_network_table = array();
    protected $colocados_network_table = array();
    protected $frontales_network_table = array();
    protected $referidos_network_table = array();
    protected $_arrayResult = array();
    protected $_referidosResult = array();
    protected $_colocadosResult = array();
    protected $_frontalesResult = array();
	
    public function __construct()
    {
        parent::__construct();
        
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();        
        $item = isset($_GET['item']) ? $_GET['item'] : 1;        
        switch ($item) {
            case 1:
                $this->_arrayResult = Mage::helper('affiliate')->treeNetwork($customer_id, '', null, 0);
                break;
            case 2:
                $this->_arrayResult = Mage::helper('affiliate')->affiliateNetwork($customer_id, '', null, 0);
                break;
            case 3:
                $this->_arrayResult = Mage::helper('affiliate')->frontalNetwork($customer_id, '', null, 0);
                break;
            case 4:
                $this->_arrayResult = Mage::helper('affiliate')->treeNetwork($customer_id, '', null, 0);
                break;
            default:
                $this->_arrayResult = Mage::helper('affiliate')->treeNetwork($customer_id, '', null, 0);
                break;
        }       
        
        Mage::register('items', $this->_arrayResult);
        Mage::register('flag', $item);
    }
    
    public function getAffiliateParents($customer_id)
    {   
        $result = array();
        if($customer_id) {
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_invited',$customer_id)
                ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
                ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);

            $result = array_diff($collection->getAllIds(),array($customer_id));
        }
        
        return $result;
    }
	
    public function getSizeAffiliateNetwork()
    {   
        $customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        if($customer_id) {
            return sizeof($this->getAffiliateParents($customer_id));
        } 
        else return 0;
    }
    
	
    public function showSubAffiliateNetwork($customer_id)
    {   
        $result = '';
        if(!in_array($customer_id, $this->array_network)) {
            $this->array_network[] = $customer_id;

            $size = sizeof($this->getAffiliateParents($customer_id));
            $customer_childs = $this->getAffiliateParents($customer_id);
            $name = Mage::getModel('customer/customer')->load($customer_id)->getName();

            $result = '<li style="padding-left:20px;font-weight:bold; list-style:inside;">' . $name;
            if($size > 0) {
                $result .= '<ul>';
                foreach ($customer_childs as $customer_child) {
                        $result .= $this->showSubAffiliateNetwork($customer_child);
                }
                $result .= '</ul>';
            }
            $result .= '</li>';
        }
        
        return $result;
    }
    
    public function showSubAffiliateNetworkTable($customer_id, $referral, $parent_id, $i, $vgp)
    {  
        if(!in_array($customer_id, $this->array_network_table)) {
            $this->array_network_table[] = $customer_id;

            $size = sizeof($this->getAffiliateParents($customer_id));
            $customer_childs = $this->getAffiliateParents($customer_id);
            
            $customer           = Mage::getModel('customer/customer')->load($customer_id);
            $name               = $customer->getName();
            $name_id            = $customer->getId();
            $email              = $customer->getEmail();
            $affiliateCustomer  = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
            $customerCommission = $affiliateCustomer->getTotalCommission();
            $customerJoinedDate = $affiliateCustomer->getCustomerTime();
            $customerStatus	= $affiliateCustomer->getActive(); 
            $code               = $affiliateCustomer->getReferralCode();                         
            $approve            = $affiliateCustomer->getAutoapprove();                         
            $name_id            = $code;
            $sponsor            = $affiliateCustomer->getReferralSponsor(); 
            $customer_photo     = $affiliateCustomer->getCustomerPhoto();
            $customer_photo     = $customer_photo ? $customer_photo : $this->getSkinUrl('images/avatar.png');

            $affiliate_group    = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                ->addFieldToFilter('customer_id', Mage::getModel('customer/customer')->load($customer_id)->getId())->getFirstItem();
            $affiliate_group    = Mage::getModel('affiliate/affiliategroup')->getCollection()
                ->addFieldToFilter('group_id',$affiliate_group['group_id'])->getFirstItem();
            
            
            $dayFirst   = date("Y-m-d 00:00:00", strtotime("first day of last month"));
            $dayLast    = date("Y-m-d 23:59:59", strtotime("last day of last month"));
            
            $sale = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('updated_at', array('from'=>$dayFirst, 'to'=>$dayLast))
                ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
                ->addAttributeToFilter('customer_id', array('eq' => $customer_id));    
            
            $vp = 0;
            $vg = 0;
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
            
//            $month_date = strtotime(date('01/m/Y 00:00'));
//            $volume             = Mage::getModel('affiliate/affiliatevolume')->getCollection()
//                                    ->addFieldToFilter('month_date', $month_date)
//                                    ->addFieldToFilter('customer_id', $customer_id)
//                                    ->getFirstItem()
//                                    ->getData();
//
//            $vp = isset($volume['vp']) ? $volume['vp'] : '0';
//            $vg = isset($volume['vg']) ? $volume['vg'] : '0';
            
            $group_name         = $affiliate_group->getData();
            $statusOptions      = MW_Affiliate_Model_Statusactive::getOptionArray();
            
            $color = '#166700';
            
            if ($vp > 0){
                if ($approve  == 1){
                    $approve = 3;
                    Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id)
                    ->setAutoapprove(3)
                    ->save();
                }
            }else{
                $color = '#ff0000';
            }
            
            if ($approve  == 1){
                $color = '#4F2F4F';
            }
            
            if ($group_name['group_name'] == "CLIENTE PREFERENTE"){
                $color = '#FFFF33';
            }

            $percentage = number_format(($vp * 100)/$vgp,2);
            //if($referral != '') {
                $this->_arrayResult[] = array(
                    'level'		=> $i, 
                    'id'		=> $code,//$name_id, 
                    'image'		=> $customer_photo, 
                    'name'		=> $name,
                    'sponsor'           => $sponsor,
                    'title'		=> $name,
                    'referral'          => $referral,
                    'commission'	=> $customerCommission,
                    'joined_date'	=> $customerJoinedDate,
                    'parent'            => $parent_id,
                    'rank'              => $group_name['group_name'],
                    'code'              => $code,
                    'personaactive'     => 'true',
                    'status'            => $statusOptions[$customerStatus],
                    'description'	=> "Rango: {$group_name['group_name']}",
                    'itemTitleColor'    => $color,
                    'template'          => 'contactTemplate',
                    'vp'                => "VP: {$vp}",
                    'vg'                => "VG: {$vg}",
                    'vgp'               => "{$percentage}%",
                );
            //}
            
            //INACTIVES
            if (Mage::getStoreConfig('affiliate/config/inactives',Mage::app()->getStore()) == 1){
                $inactive = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('customer_invited',$customer_id)
                    ->addFieldToFilter('active',1);
                $inactive = $inactive->getData();

                $c = 0;
                foreach ($inactive as $key => $value) {
                    $c++;
                    $name = Mage::getModel('customer/customer')->load($value['customer_id'])->getName();
                    $this->_arrayResult[] = array(
                        'level'             => $i,
                        'parent'            => $code,
                        'sponsor'           => $value['referral_sponsor'],
                        'id'                => $code.'inactive-'.$c,
                        'code'              => $code.'inactive-'.$c,
                        'name'              =>  $name,
                        'title'             => $name,
                        'image'             => $this->getSkinUrl('images/avatar.png'),
                        'personaactive'     => 'true',
                        'description'       => "Rango: -\n\nCodigo: -",
                        'itemTitleColor'    => "#eee",
                        'vp'                => "VP: 0",
                        'vg'                => "VG: 0",
                    );
                }
            }

            if($size > 0) {
                $i = $i + 1 ;
                foreach($customer_childs as $customer_child) {
                    $this->showSubAffiliateNetworkTable($customer_child, $name, $name_id, $i, $vg);
                }
            }
        }
    }
        
        
	public function showNumberArrows($level)
	{
		$arrow = $image_arrow = '';
		$package_url = Mage::getSingleton('core/design_package')->getPackageName();
		$theme_url = Mage::getSingleton('core/design_package')->getTheme('frontend');
		
		for($i=2; $i<=(int)$level;$i++)
		{
			if($i==2)
				$image_arrow = 'mw_affiliate/images/line.gif';
			if($i>2)
				$image_arrow = 'mw_affiliate/images/line2.gif';
			$img_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/'.$package_url.'/'.$theme_url.'/'.$image_arrow;
			$arrow.= "<img src='".$img_url."' />";
		}
		return $arrow;
	}

    public function _prepareLayout()
    {
        parent::_prepareLayout();

//        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_affiliate_network')
//                              ->setCollection($this->getAffiliateNetworkCollection());	
//        $this->setChild('pager', $pager);
//        $this->getCollection()->load();
        
        return $this;
    }
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
 	public function getCollection()
    {
    	return $this->getChild("pager")->getCollection();
    }
}
