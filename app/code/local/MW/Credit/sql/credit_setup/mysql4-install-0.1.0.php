<?php
$installer = $this;
$collection = Mage::getModel('credit/credithistory')->getCollection();
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$collection->getTable('credithistory')};
CREATE TABLE {$collection->getTable('credithistory')} (
  `credit_history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL default '0',
  `type_transaction` int(2) NOT NULL default '0',
  `transaction_detail` varchar(255) NOT NULL default '',
  `amount` double(15,2) NOT NULL default '0.00',
  `beginning_transaction` double(15,2) NOT NULL default '0.00',
  `end_transaction` double(15,2) NOT NULL default '0.00',
  `created_time` datetime NULL,
  `status` int(2) NULL default '0',
  PRIMARY KEY (`credit_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('creditcustomer')};
CREATE TABLE {$collection->getTable('creditcustomer')} (
  `customer_id` int(11) NOT NULL default '0',
  `credit` double(15,2) NOT NULL default '0.00',
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('creditorder')};
CREATE TABLE {$collection->getTable('creditorder')} (
  `order_id` varchar(255) NOT NULL default '',
  `credit` double(15,2) NOT NULL default '0.00',
  `affiliate` double(15,2) NOT NULL default '0.00',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$customers = Mage::getModel('customer/customer')->getCollection();

$sql ="";
foreach($customers as $customer)
{
	$sql .="INSERT INTO {$collection->getTable('creditcustomer')} 
				VALUES(".$customer->getId().",0);";
}
$installer->run($sql);

$installer->endSetup(); 