<?php

class MW_Affiliate_Helper_Binary_Data extends Mage_Core_Helper_Abstract {

    protected $_treeResult = array();
    protected $tree_network_table = array();
    protected $orders = array();
    protected $leftleg = 0;
    protected $rightleg = 0;

    public function setExpiredAutomatic($order) {
        $entity = $order->getEntityId();
        $historial = Mage::getResourceModel('sales/order_status_history_collection')
                ->addAttributeToFilter('main_table.status', array('eq' => 'complete'))
                ->addAttributeToFilter('main_table.entity_name', array('in' => array('invoice', 'shipment')))
                ->addAttributeToFilter('main_table.parent_id', array('in' => array($entity)))
                ->getFirstItem();

        $expired = date("Y-m-d 23:59:59", strtotime($historial->getData('created_at') . ' +1 Month'));
        $historic = json_encode(array(array('order' => $entity)));
        $customer_id = $order->getCustomerId();

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('recompra_notification');
        $emailTemplateVariables = array();
        $emailTemplateVariables['nueva_fecha'] = $expired;
        $emailTemplateVariables['email'] = $order->getCustomerEmail();
        $emailTemplateVariables['link'] = Mage::helper("adminhtml")->getUrl("affiliate/adminhtml_affiliatebinary/editexpired", array("id" => $customer_id));
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $subject = 'Expiracion de Recompra';

        $expired_package= Mage::getModel('affiliate/affiliateexpired')->load($customer_id);
                
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        if($expired_package['expired_package']){
            $sql = "UPDATE mw_affiliate_expired SET expired_package = '{$expired}', historic='{$historic}' WHERE customer_id = {$customer_id}";
        }
        else{
            $sql = "INSERT INTO mw_affiliate_expired VALUES ({$customer_id}, '{$expired}', NULL, '{$historic}')";
        }
        
        $write->query($sql);
        $mail = Mage::getModel('core/email')
            ->setToEmail(array('info@habitossaludables.com.mx'))
            ->setBody($processedTemplate)
            ->setSubject($subject)
            ->setFromEmail('info@habitossaludables.com.mx')
            ->setFromName('Habitos Saludables')
            ->setType('html')
            ->send();
    }

    public function getPreferredCustomers() {
        $preferred_customer = Mage::getModel('affiliate/affiliategroupmember')
                ->getCollection()
                ->addFieldToSelect('customer_id')
                ->addFieldToFilter('group_id', array('eq' => 13))
                ->getData();

        return $preferred_customer;
    }

    public function getDirectPreferredCustomers($code) {
        $preferred_sponsor = Mage::getModel('affiliate/affiliatecustomers')
                ->getCollection()
                ->addFieldToSelect('customer_id')
                ->addFieldToFilter('customer_id', array('in' => array($this->getPreferredCustomers())))
                ->addFieldToFilter('main_table.active', 2)
                ->addFieldToFilter('main_table.referral_sponsor', $code);

        return $preferred_sponsor;
    }

    public function binarychangeuser($var) {
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('user_modification_binary');
        $emailTemplateVariables = array();
        $emailTemplateVariables['customer_name'] = $var;
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $subject = 'Modificacion del Binario';

        $mail = Mage::getModel('core/email')
                ->setToEmail(array('vika@habitossaludables.com.mx'))
                ->setBody($processedTemplate)
                ->setSubject($subject)
                ->setFromEmail('info@habitossaludables.com.mx')
                ->setFromName('Habitos Saludables')
                ->setType('html')
                ->send();
    }

    public function binaryHoldTank($customer_id) {
        $sponsor = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_id', $customer_id)
                ->getFirstItem();

        $hold = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('referral_sponsor', $sponsor['referral_code'])
                ->addFieldToFilter('anetwork', array('gteq' => 2));

        $array = array();
        foreach ($hold->getData() as $key => $value) {
            $referral = $value['referral_code'];

            $check = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('anetwork', array('gteq' => 2));

            $check->getSelect()
                    ->where('left_leg = ?', $referral)
                    ->orWhere('right_leg = ?', $referral);

            if (sizeof($check->getData()) == 0) {
                array_push($array, $value);
            }
        }

        return $array;
    }

    public function binaryNetwork($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay) {
        $this->_treeResult = array();
        $this->tree_network_table = array();
        $this->orders = array();
        $this->binaryNetworkTable($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay, 'm');

        $array = $this->_treeResult;
        foreach ($array as $key => $item) {
            $total = $item['vg'];
            $id = $item['id'];
            foreach ($array as $k => $child) {
                if ($id == $child['parent']) {
                    $array[$k]['percentage'] = number_format(((100 * ($child['vg'] + $child['vp'])) / $total), 2) . '%';
                }
            }
        }

        return $array;
    }

    public function binaryDirectNetwork($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay) {
        $this->binaryDirectNetworkTable($customer_id, $parent_id, $parent_id, $i, $firstDay, $lastDay);
        $array = $this->_treeResult;
        foreach ($array as $key => $item) {
            $total = $item['vg'];
            $id = $item['id'];
            foreach ($array as $k => $child) {
                if ($id == $child['parent']) {
                    $array[$k]['percentage'] = number_format(((100 * ($child['vg'] + $child['vp'])) / $total), 2) . '%';
                }
            }
        }

        return $array;
    }

    public function binaryBlank($parent_id, $i, $side) {
        $tmp = $this->networkBlank($i, $parent_id, $side);
        $this->_treeResult[] = $tmp;
        return 0;
    }

    public function binaryNetworkTable($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay, $leg) {
        if (!in_array($customer_id, $this->tree_network_table)) {
            $this->tree_network_table[] = $customer_id;

            $vp = 0;
            $vg = 0;
            $izq = 0;
            $der = 0;

            $tmp = $this->networkInfo($customer_id, $parent_id, $i, 2, $firstDay, $lastDay, $leg);
            if (sizeof($tmp) > 0) {
                $vp = $tmp['vp'] ? $tmp['vp'] : 0;
                $left = $this->getBinaryLegs($tmp['left'], $customer_id, false);
                $right = $this->getBinaryLegs($tmp['right'], $customer_id, false);

                $p = $i;
                $x = $i;

                if (sizeof($left) > 0) {
                    $x = $x + 1;
                    foreach ($left as $child) {
                        $izq = $izq + $this->binaryNetworkTable($child, $tmp['name'], $tmp['code'], $x, $firstDay, $lastDay, 'l');
                    }
                } else {
                    if (sizeof($right) > 0) {
                        $x = $x + 1;
                        $izq = $izq + $this->binaryBlank($tmp['code'], $x, 'l');
                    }
                }

                if (sizeof($right) > 0) {
                    $p = $p + 1;
                    foreach ($right as $child) {
                        $der = $der + $this->binaryNetworkTable($child, $tmp['name'], $tmp['code'], $p, $firstDay, $lastDay, 'r');
                    }
                } else {
                    if (sizeof($left) > 0) {
                        $p = $p + 1;
                        $der = $der + $this->binaryBlank($tmp['code'], $p, 'r');
                    }
                }

                $vg = $izq + $der;
                $tmp['vg'] = "{$vg}" ? "{$vg}" : "0";
                $tmp['vleft'] = "{$izq}" ? "{$izq}" : "0";
                $tmp['vpright'] = "{$der}" ? "{$der}" : "0";
                if ($i == 0) {
                    $tmp['package'] = $this->orders;
                }
                $this->_treeResult[] = $tmp;
            }
        }

        return $vp + $vg;
    }

    public function binaryDirectNetworkTable($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay, $leg) {
        if (!in_array($customer_id, $this->tree_network_table)) {
            $this->tree_network_table[] = $customer_id;

            $vp = 0;
            $vg = 0;

            $firstDay = date("Y-m-d 00:00:00", strtotime("last friday"));
            $lastDay = date("Y-m-d 23:59:59", strtotime("last thursday"));
            $tmp = $this->networkInfo($customer_id, $referral, $i, 2, $firstDay, $lastDay, $leg);

            if (sizeof($tmp) > 0) {
                $vp = $tmp['vp'];
                $right = $this->getBinaryDirect($tmp['code'], $customer_id, true);

                $p = $i;
                if (sizeof($right) > 0) {
                    $p = $p + 1;
                    foreach ($right as $child) {
                        $vg = $vg + $this->binaryDirectNetworkTable($child, $tmp['code'], $tmp['code'], $p, $firstDay, $lastDay);
                    }
                }

                $tmp['vg'] = $vg;

                $this->_treeResult[] = $tmp;
            }
        }

        return $vp + $vg;
    }

    public function getBinaryDirect($code, $customer_id, $direct) {
        $result = array();
        if ($code) {

            $preferred_customer = Mage::getModel('affiliate/affiliategroupmember')
                    ->getCollection()
                    ->addFieldToSelect('customer_id')
                    ->addFieldToFilter('group_id', array('eq' => 13))
                    ->getData();

            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('referral_sponsor', $code)
                    ->addFieldToFilter('status ', MW_Affiliate_Model_Statusreferral::ENABLED)
                    ->addFieldToFilter('customer_id ', array('nin' => $preferred_customer))
                    ->addFieldToFilter('active', MW_Affiliate_Model_Statusactive::ACTIVE)
                    ->addFieldToFilter('anetwork', array('gteq' => 2));

            $result = array_diff($collection->getAllIds(), array($customer_id));
        }

        return $result;
    }

    public function getBinaryLegs($code, $customer_id, $direct) {
        $result = array();
        if ($code) {
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('referral_code', $code)
                    ->addFieldToFilter('status ', MW_Affiliate_Model_Statusreferral::ENABLED)
                    ->addFieldToFilter('active', MW_Affiliate_Model_Statusactive::ACTIVE);

            $result = array_diff($collection->getAllIds(), array($customer_id));
        }

        return $result;
    }

    public function networkBlank($i, $parent_id, $side) {
        $extra = $side == "l" ? "a" : "b";
        $array = array(
            'level' => $i,
            'id' => "{$parent_id}-{$extra}",
            'code' => "{$parent_id}-{$extra}",
            'image' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/hs/default/images/avatar.png',
            'title' => "PATROCINAR",
            'name' => "PATROCINAR",
            'parent' => $parent_id,
            'sponsor' => 0,
            'right' => null,
            'left' => null,
            'rank' => "",
            'status' => false,
            'personaactive' => 'false',
            'itemTitleColor' => '#000',
            'templateName' => 'contactTemplate2',
            'vp' => "0",
            'vg' => "0",
            'vleft' => "0",
            'vpright' => "0",
            'codeurl' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . Mage::app()->getStore()->getCode() . "/affiliate/binary/registry/s/{$side}/n/{$parent_id}",
        );

        return $array;
    }

    public function customerInformation($customer_id, $i, $leg, $parent_id) {
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $affiliate = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
        $code = $affiliate->getReferralCode();
        $name = $customer->getName();
        $sponsor = $affiliate->getReferralSponsor();
        $image = $affiliate->getCustomerPhoto() ? $affiliate->getCustomerPhoto() : Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/hs/default/images/avatar.png';
        $group_member = Mage::getModel('affiliate/affiliategroupmember')->getCollection()->addFieldToFilter('customer_id', $customer_id)->getFirstItem();
        $group = Mage::getModel('affiliate/affiliategroup')->getCollection()->addFieldToFilter('group_id', $group_member['group_id'])->getFirstItem();
        $group_name = $group->getData('group_name');

        $info = array(
            'level' => $i,
            'leg' => $leg,
            'customer_id' => $customer_id,
            'id' => $code,
            'code' => $code,
            'image' => $image,
            'title' => $name,
            'name' => $name,
            'parent' => $parent_id,
            'sponsor' => $sponsor,
            'right' => $affiliate->getRightLeg() ? $affiliate->getRightLeg() : null,
            'left' => $affiliate->getLeftLeg() ? $affiliate->getLeftLeg() : null,
            'rank' => $group_name,
            'personaactive' => 'true',
            'templateName' => 'contactTemplate',
            'vp' => 0,
            'vg' => 0,
            'vleft' => 0,
            'vpright' => 0,
            'constructor' => 0,
        );

        return $info;
    }

    public function networkInfo($customer_id, $parent_id, $i, $type, $firstDay, $lastDay, $leg) {
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $expired_package = Mage::getModel('affiliate/affiliateexpired')->load($customer_id);
        $affiliate = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
        $code = $affiliate->getReferralCode();
        $name = $customer->getName();
        $sponsor = $affiliate->getReferralSponsor();
        $invited = $affiliate->getCustomerInvited();
        $sponsor_id = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('referral_code', $code)->getFirstItem()->getData("customer_invited");
        $customerStatus = $affiliate->getActive();
        $image = $affiliate->getCustomerPhoto() ? $affiliate->getCustomerPhoto() : Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/hs/default/images/avatar.png';
        $group_member = Mage::getModel('affiliate/affiliategroupmember')->getCollection()->addFieldToFilter('customer_id', $customer_id)->getFirstItem();
        $group = Mage::getModel('affiliate/affiliategroup')->getCollection()->addFieldToFilter('group_id', $group_member['group_id'])->getFirstItem();
        $group_name = $group->getData('group_name');
        $vp = 0;
        $vg = 0;
        $vpleft = 0;
        $vpright = 0;
        $constructor = 0;
        $color = '#166700';
        $statusOptions = MW_Affiliate_Model_Statusactive::getOptionArray();

        $array = array();

        if ($i == 0):
            $today = strtotime($lastDay);
            $last_pkt = strtotime($expired_package['expired_package'] . ' -1 Month');
            $firstDay = date("Y-m-d 00:00:00", strtotime("first day of this month"));

            if ($expired_package['expired_package']) {
                if ($last_pkt <= $today) {
                    $diff = abs($today - $last_pkt);
                    $days = floor(($diff) / (60 * 60 * 24));

                    if ($days <= 30) {
                        $firstDay = date("Y-m-d 00:00:00", $last_pkt);
                    }
                }
            }
        endif;

        if (!($group_name == "CLIENTE PREFERENTE")) {
            $historial = Mage::getResourceModel('sales/order_status_history_collection')
                    ->addAttributeToSelect('parent_id')
                    ->addAttributeToFilter('main_table.status', array('eq' => 'complete'))
                    ->addAttributeToFilter('main_table.created_at', array('from' => $firstDay, 'to' => $lastDay))
                    ->addAttributeToFilter('main_table.entity_name', array('in' => array('invoice', 'shipment')));

            if ($historial->getData('parent_id')):
                $sale = Mage::getModel('sales/order')->getCollection()
                        ->addAttributeToFilter('customer_id', array('eq' => $customer_id))
                        ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                        ->addAttributeToFilter('affiliate_network', array('eq' => $type));

                foreach ($sale as $order) {
                    $items = $order->getAllVisibleItems();
                    array_push($this->orders, array($order->getIncrementId()));
                    foreach ($items as $item) {
                        $single = $item->getData();
                        $puntos = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos');

                        if ($single['product_type'] == 'bundle') {
                            if (array_search($single['sku'], array('PTK-USA-010', 'PKT-USA-009', 'PKT-USA-008', 'PKT-USA-007', 'PKT-REC'))) {
                                $puntos = $puntos;
                            } else {
                                $puntos = $puntos / 2;
                            }
                        }

                        if ($order->getAffiliateSaleType() == 3) { //constructor
                            $constructor = $puntos * $single['qty_ordered'];
                            $puntos = $puntos * .25;
                        }

                        $vp = $vp + ($puntos * $single['qty_ordered']);
                    }
                }
            endif;

            if ($vp > 0) {
                if ($approve == 1) {
                    $approve = 3;
                    Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id)->setAutoapprove(3)->save();
                }
            } else {
                $color = '#ff0000';
            }

            if ($approve == 1) {
                $color = '#4F2F4F';
            }

            $array = array(
                'level' => $i,
                'leg' => $leg,
                'id' => $code,
                'code' => $code,
                'image' => $image,
                'title' => $name,
                'name' => $name,
                'parent' => $parent_id,
                'sponsor' => $sponsor,
                'right' => $affiliate->getRightLeg() ? $affiliate->getRightLeg() : null,
                'left' => $affiliate->getLeftLeg() ? $affiliate->getLeftLeg() : null,
                'rank' => $group_name,
                'status' => $statusOptions[$customerStatus],
                'personaactive' => 'true',
                'itemTitleColor' => $color,
                'templateName' => 'contactTemplate',
                'vp' => "{$vp}",
                'vg' => "{$vg}",
                'vleft' => "{$vpleft}",
                'vpright' => "{$vpright}",
                'constructor' => "{$constructor}",
            );
        }

        return $array;
    }

    //Binary Residual    
    public function getResidualReport($dayFirst, $filterFirst, $dayLast) {
        $collection = Mage::getModel('affiliate/affiliatecustomers')
                ->getCollection()
                ->addFieldToFilter('anetwork', array('in' => array(2, 3)))
                ->addFieldToFilter('active', 2)
//            ->addFieldToFilter('customer_id', array('from'=>2000, 'to'=>3000))
//            ->addFieldToFilter('customer_id', array('from'=>1000, 'to'=>2000))
//            ->addFieldToFilter('customer_id',array('gt' => 1323))
//            ->addFieldToFilter('customer_id',23)
                ->getData();

        foreach ($collection as $key => $value) {
            $customer_id = $value['customer_id'];
            $code = $value['referral_code'];

            $network = Mage::helper('affiliate_binary')->binaryNetwork($customer_id, '', null, 0, $dayFirst, $dayLast);

            if ($network) {
                $item = array_pop($network);
                $insert = array(
                    "customer_id" => $value['customer_id'],
                    "vp" => $item['vp'] ? $item['vp'] : 0,
                    "vg" => $item['vg'] ? $item['vg'] : 0,
                    "constructor" => $item['constructor'] ? $item['constructor'] : 0,
                    "left" => $item['vleft'] ? $item['vleft'] : 0,
                    "right" => $item['vpright'] ? $item['vpright'] : 0,
                    "package" => json_encode($item['package']),
                    "paid" => 0,
                    "hold" => 0,
                    "hold_side" => "",
                    "cp" => 0,
                    "start_date" => $filterFirst,
                    "end_date" => $dayLast,
                );

                $historial = Mage::getResourceModel('sales/order_status_history_collection')
                        ->addAttributeToSelect('parent_id')
                        ->addAttributeToFilter('main_table.status', array('eq' => 'complete'))
                        ->addAttributeToFilter('main_table.created_at', array('from' => $dayFirst, 'to' => $dayLast))
                        ->addAttributeToFilter('main_table.entity_name', array('in' => array('invoice', 'shipment')));

                $preferred_customer = Mage::getModel('affiliate/affiliategroupmember')
                        ->getCollection()
                        ->addFieldToSelect('customer_id')
                        ->addFieldToFilter('group_id', array('eq' => 13))
                        ->getData();

                $t = array();
                $cp = 0;

                $t = array();
                $code = Mage::getModel('affiliate/affiliatecustomers')
                        ->getCollection()
                        ->addFieldToFilter('customer_id', $value['customer_id'])
                        ->getFirstItem()
                        ->getData('referral_code');

                $preferred_sponsor = Mage::getModel('affiliate/affiliatecustomers')
                        ->getCollection()
                        ->addFieldToSelect('customer_id')
                        ->addFieldToFilter('customer_id', array('in' => array($preferred_customer)))
                        ->addFieldToFilter('main_table.active', 2)
                        ->addFieldToFilter('main_table.referral_sponsor', $code);

                if (sizeof($preferred_sponsor) > 0) {
                    $sale = Mage::getModel('sales/order')->getCollection()
                            ->addAttributeToFilter('customer_id', array('in' => array($preferred_sponsor->getData('customer_id'))))
                            ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                            ->addAttributeToFilter('affiliate_network', array('eq' => 2));

                    foreach ($sale as $order) {
                        $items = $order->getAllVisibleItems();
                        array_push($t, array($order->getIncrementId()));
                        foreach ($items as $item) {
                            $single = $item->getData();
                            $puntos = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos');

                            if ($single['product_type'] == 'bundle') {
                                if (array_search($single['sku'], array('PTK-USA-010', 'PKT-USA-009', 'PKT-USA-008', 'PKT-USA-007', 'PKT-REC'))) {
                                    $puntos = $puntos;
                                } else {
                                    $puntos = $puntos / 2;
                                }
                            }

                            $cp = $cp + ($puntos * $single['qty_ordered']);
                        }
                    }
                }


                $insert['cp'] = $cp;
                $insert['package_cp'] = json_encode($t);

                echo '<pre>';
                print_R($insert);
                Mage::getModel('affiliate/affiliatebinaryresidual')->saveResidual($insert);
            }
        }
    }

    public function getBinaryCheck($filterFirst) {
        $data = Mage::getModel('affiliate/affiliatebinaryresidual')
                ->getCollection()
                ->addFieldToFilter('start_date', array('eq' => $filterFirst))
//            ->addFieldToFilter('customer_id', array('eq'=>28))
                ->getData();

        foreach ($data as $key => $value) {
            if ($value['paid'] > 0) {
                $points = $value['paid'];
                $customer_id = $value['customer_id'];

                $sponsor = Mage::helper('affiliate_binary')->getSponsorResidual($customer_id, $filterFirst);

                if ($sponsor['success'] == 1) {
                    $profit = $points * $sponsor['rank'];
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $sql = "INSERT INTO mw_affiliate_binary_check (start_date, end_date, customer_id, profit, level, chain) "
                            . "VALUES ('{$filterFirst}', '{$value['end_date']}', {$sponsor['customer_id']}, {$profit}, 1, {$value['id']})";
                    $write->query($sql);
                } else {
                    $next_sponsor = Mage::helper('affiliate_binary')->getSponsorResidual($sponsor['customer_id'], $filterFirst);
                    if ($next_sponsor['success'] == 1) {
                        $profit = $points * $next_sponsor['rank'];
                        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                        $sql = "INSERT INTO mw_affiliate_binary_check (start_date, end_date, customer_id, profit, level, chain) "
                                . "VALUES ('{$filterFirst}', '{$value['end_date']}', {$next_sponsor['customer_id']}, {$profit}, 2, {$value['id']})";
                        $write->query($sql);
                    }
                }
            }
        }
    }

    public function getResidualBonus($filterFirst, $filterLast) {
        $data = Mage::getModel('affiliate/affiliatebinaryresidual')
                ->getCollection()
                ->addFieldToFilter('start_date', array('eq' => $filterFirst))
//            ->addFieldToFilter('customer_id', array('eq'=>23))
//            ->addFieldToFilter('customer_id', array('eq'=>38))
                ->getData();

        $percentage = array(
            1 => 0,
            2 => .10,
            3 => .12,
            4 => 0,
            5 => .15,
            6 => .17,
            7 => 0,
            8 => .20,
            9 => .20,
            10 => .20,
            11 => .20,
            12 => .20,
            13 => 0,
        );

        foreach ($data as $key => $value) {
            $customer_id = $value['customer_id'];
            $cp = $value['cp'] * .25;
            $left = $value['vpleft'];
            $right = $value['vpright'];
            $vp = $value['vp'];
            $vg = $value['vg'];

            $rank = $percentage[Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                            ->addFieldToFilter('customer_id', array('in' => $customer_id))
                            ->getFirstItem()
                            ->getData('group_id')];

            $code = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('customer_id', array('in' => $customer_id))
                    ->getFirstItem()
                    ->getData('referral_code');

            /* */
            $directos = Mage::helper('affiliate_binary')->getBinaryDirect($code, $customer_id, false);

            $actives = 0;
            $actives_array = array();
            if ($directos) {
                $historial = Mage::getResourceModel('sales/order_status_history_collection')
                        ->addAttributeToSelect('parent_id')
                        ->addAttributeToFilter('main_table.status', array('eq' => 'complete'))
                        ->addAttributeToFilter('main_table.created_at', array('from' => date("Y-m-d 00:00:00", strtotime("first day of last month")), 'to' => $filterLast))
                        ->addAttributeToFilter('main_table.entity_name', array('in' => array('invoice', 'shipment')));

                if (sizeof($historial->getData()) > 0) {
                    $info = Mage::getModel('sales/order')->getCollection()
                            ->addAttributeToFilter('customer_id', array('in' => array($directos)))
                            ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                            ->addAttributeToFilter('affiliate_network', array('eq' => 2));

                    foreach ($info as $order) {
                        $actives = $actives + 1;
                        array_push($actives_array, array($order->getIncrementId()));
                    }
                }
            }
            /* */

            $tleft = 0;
            $tright = 0;
            $tvg = 0;
            $tcp = 0;
            $thold = 0;
            $past_records = Mage::getModel('affiliate/affiliatebinaryresidual')
                    ->getCollection()
                    ->addFieldToFilter('customer_id', array('eq' => $customer_id))
                    ->addFieldToFilter('start_date', array('lt' => $filterFirst))
                    ->setOrder('start_date', 'DESC')
                    ->getData();

            for ($i = 0; $i < sizeof($past_records); $i++) {
                if (($past_records[$i]['flag'] == 1) && ($i == 0)) {  //Si pago la pasada y es la primera :)
                    $tcp = $tcp + $past_records[$i]['cp'];
                    if ($past_records[$i]['hold_side'] == 2) {
                        $right = $right + $past_records[$i]['hold'];
                    } else if ($past_records[$i]['hold_side'] == 1) {
                        $left = $left + $past_records[$i]['hold'];
                    }
                    $i = sizeof($past_records) + 1;
                }


                if ($past_records[$i]['flag'] == 0) {
                    $tcp = $tcp + ($past_records[$i]['cp'] * .25);
                    if ($past_records[$i]['hold_side'] == 3) {
                        $left = $left + $past_records[$i]['vpleft'];
                        $right = $right + $past_records[$i]['vpright'];
                    } else if ($past_records[$i]['hold_side'] == 2) {
                        $right = $right + $past_records[$i]['vpright'] + $cp;
                    } else if ($past_records[$i]['hold_side'] == 1) {
                        $left = $left + $past_records[$i]['vpleft'] + $cp;
                    }
                } else {
                    $thold = $thold + $past_records[$i]['hold'];
                    $i = sizeof($past_records) + 1;
                }
            }

            $cp = $tcp + $cp;
            $weak_points = ($left == $right) ? 3 : (($left > $right) ? 2 : 1);
            $paid = 0;
            $hold = 0;
            $flag = 0;
            $hold_side = 3;

            if ($weak_points == 1) {
                $left = $left + $cp;
            } else if ($weak_points == 2) {
                $right = $right + $cp;
            }

            if ($thold > 0) {
                if ($weak_points == 1) {
                    $right = $right + $thold;
                } else if ($weak_points == 2) {
                    $left = $left + $thold;
                }
            }

            if (($left >= 100) && ($right >= 100)) {
                if (($vp >= 50) && ($actives > 0)) {
                    if ($weak_points == 1) {
                        $hold = ($right - $left) < 0 ? 0 : ($right - $left);
                        $paid = $left * $rank;
                        $flag = 1;
                        $hold_side = 2;
                    } elseif ($weak_points == 2) {
                        $hold = ($left - $right) < 0 ? 0 : ($left - $right);
                        $paid = $right * $rank;
                        $flag = 1;
                        $hold_side = 1;
                    } else {
                        $right = $right + $cp;
                        $right = $right + $thold;

                        $hold = 0;
                        $paid = $right * $rank;
                        $flag = 1;
                        $hold_side = 0;
                    }
                }
            }

            $tleft = $left;
            $tright = $right;
            $tvg = $left + $right;

            $result = array(
                'customer_id' => $customer_id,
                'paid' => $paid,
                'actives' => $actives,
                'cp' => $cp,
                'actives_orders' => json_encode($actives_array)
            );

            $actives_array = json_encode($actives_array);
            echo '<pre>';
            print_R($result);
//            echo $actives;
//            
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql = "UPDATE mw_affiliate_binary_residual SET active_orders = '{$actives_array}',  tleft={$tleft}, tright = {$tright}, tvg = {$tvg}, paid = {$paid}, hold = {$hold}, flag = {$flag}, hold_side = {$hold_side} WHERE customer_id = {$customer_id} and start_date = '{$filterFirst}'";
            $write->query($sql);
        }
    }

    public function getSponsorResidual($customer_id, $filterFirst) {
        $percentage = array(
            1 => 0,
            2 => 0,
            3 => .20,
            4 => 0,
            5 => .30,
            6 => .40,
            7 => 0,
            8 => .50,
            9 => .50,
            10 => .50,
            11 => .50,
            12 => .50,
            13 => 0,
        );

        $code = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_id', array('in' => $customer_id))
                ->getFirstItem()
                ->getData('referral_sponsor');

        $sponsor = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('referral_code', array('in' => $code))
                ->getFirstItem()
                ->getData();

        $sponsor_sale = Mage::getModel('affiliate/affiliatebinaryresidual')->getCollection()
                ->addFieldToFilter('customer_id', array('in' => array($sponsor['customer_id'])))
                ->addFieldToFilter('start_date', array('eq' => $filterFirst))
                ->getFirstItem()
                ->getData();

        if ((sizeof($sponsor_sale) > 0) || ($code == 10000)) {
            $rank = $percentage[Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                            ->addFieldToFilter('customer_id', array('in' => $sponsor['customer_id']))
                            ->getFirstItem()
                            ->getData('group_id')];

            $directos = Mage::helper('affiliate_binary')->getBinaryDirect($sponsor['referral_code'], $sponsor['customer_id'], false);
            $actives = 0;
            if ($directos) {
                $info = Mage::getModel('affiliate/affiliatebinaryresidual')->getCollection()
                        ->addFieldToFilter('customer_id', array('in' => array($directos)))
                        ->addFieldToFilter('start_date', array('eq' => $filterFirst))
                        ->getData();

                foreach ($info as $key => $value) {
                    if ($value['vp'] > 0) {
                        $actives = $actives + 1;
                    }
                }
            }

            if ((($actives > 0) && ($sponsor_sale['vp'] > 50)) || ($code == 10000)) {
                return array('success' => 1, 'rank' => $rank, 'customer_id' => $sponsor['customer_id']);
            }
        }

        return array('success' => 0, 'customer_id' => $sponsor['customer_id']);
    }

    public function getRangeDate(){
        $today = date('D');
        switch ($today) {
            case 'Fri': $array = array(date('Y-m-d 00:00:00', strtotime('-7 Day')), date('Y-m-d 23:59:59', strtotime('-1 Day')), date('Y-m-d 08:00:00', strtotime('-7 Day'))); break;
            case 'Sat': $array = array(date('Y-m-d 00:00:00', strtotime('-8 Day')), date('Y-m-d 23:59:59', strtotime('-2 Day')), date('Y-m-d 08:00:00', strtotime('-8 Day'))); break;
            case 'Sun': $array = array(date('Y-m-d 00:00:00', strtotime('-9 Day')), date('Y-m-d 23:59:59', strtotime('-3 Day')), date('Y-m-d 08:00:00', strtotime('-9 Day'))); break;
            case 'Mon': $array = array(date('Y-m-d 00:00:00', strtotime('-10 Day')), date('Y-m-d 23:59:59', strtotime('-4 Day')), date('Y-m-d 08:00:00', strtotime('-10 Day'))); break;
            case 'Tue': $array = array(date('Y-m-d 00:00:00', strtotime('-11 Day')), date('Y-m-d 23:59:59', strtotime('-5 Day')), date('Y-m-d 08:00:00', strtotime('-11 Day'))); break;
            case 'Wed': $array = array(date('Y-m-d 00:00:00', strtotime('-12 Day')), date('Y-m-d 23:59:59', strtotime('-6 Day')), date('Y-m-d 08:00:00', strtotime('-12 Day'))); break;
            case 'Thu': $array = array(date('Y-m-d 00:00:00', strtotime('-13 Day')), date('Y-m-d 23:59:59', strtotime('-7 Day')), date('Y-m-d 08:00:00', strtotime('-13 Day'))); break;
        }
        
        return $array;
    }
}

